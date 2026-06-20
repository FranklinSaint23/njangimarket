<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // Toujours pointer vers le sandbox demo Campay
    private string $baseUrl = 'https://demo.campay.net/api';

    private function getToken(): ?string
    {
        $res = Http::acceptJson()
            ->post("{$this->baseUrl}/token/", [
                'username' => config('services.campay.username'),
                'password' => config('services.campay.password'),
            ]);

        if ($res->successful() && $res->json('token')) {
            return $res->json('token');
        }

        Log::error('Campay token failed', [
            'status'   => $res->status(),
            'response' => $res->body(),
        ]);

        return null;
    }

    // Campay utilise "Token <token>" (DRF), PAS "Bearer <token>"
    private function campayHttp(string $token)
    {
        return Http::acceptJson()
            ->withHeaders(['Authorization' => "Token {$token}"]);
    }

    public function initier(Request $request, Commande $commande)
    {
        abort_unless($commande->client_id === auth()->id(), 403);

        if (!in_array($commande->statut, ['en_attente', 'paiement_en_cours'])) {
            return redirect()->route('client.commandes.show', $commande)
                ->with('info', 'Cette commande a déjà été traitée.');
        }

        $phone = $request->phone_paiement
            ?? session('phone_paiement')
            ?? auth()->user()->phone;

        return view('client.paiement', compact('commande', 'phone'));
    }

    public function payer(Request $request, Commande $commande)
    {
        abort_unless($commande->client_id === auth()->id(), 403);

        $request->validate([
            'phone' => ['required', 'string', 'min:9', 'max:15'],
        ]);

        $token = $this->getToken();
        if (!$token) {
            return back()->with('error', 'Service de paiement indisponible. Vérifiez vos identifiants Campay.');
        }

        // Nettoyer le numéro : garder seulement les chiffres
        $phone = preg_replace('/\D/', '', $request->phone);

        $res = $this->campayHttp($token)->post("{$this->baseUrl}/collect/", [
            'amount'             => (string)(int) $commande->total,
            'currency'           => 'XAF',
            'from'               => $phone,
            'description'        => "NjangiMarket - Commande {$commande->reference}",
            'external_reference' => $commande->reference,
        ]);

        Log::info('Campay collect response', [
            'status'   => $res->status(),
            'body'     => $res->body(),
            'commande' => $commande->reference,
        ]);

        if ($res->successful() && $res->json('reference')) {
            $commande->update([
                'campay_reference' => $res->json('reference'),
                'statut'           => 'paiement_en_cours',
            ]);

            return redirect()->route('paiement.statut', $commande)
                ->with('success', 'Demande envoyée. Validez le paiement sur votre téléphone.');
        }

        $msg = $res->json('message') ?? $res->json('detail') ?? "Erreur {$res->status()}";
        Log::error('Campay collect failed', ['body' => $res->body()]);

        return back()->with('error', "Échec du paiement : {$msg}");
    }

    public function statut(Commande $commande)
    {
        abort_unless($commande->client_id === auth()->id(), 403);

        if ($commande->campay_reference) {
            $token = $this->getToken();
            if ($token) {
                $res = $this->campayHttp($token)
                    ->get("{$this->baseUrl}/transaction/{$commande->campay_reference}/");

                Log::info('Campay status check', [
                    'status'    => $res->status(),
                    'body'      => $res->body(),
                    'reference' => $commande->campay_reference,
                ]);

                if ($res->successful()) {
                    $status = $res->json('status');
                    if ($status === 'SUCCESSFUL' && $commande->statut !== 'payee') {
                        $commande->update(['statut' => 'payee']);
                    } elseif ($status === 'FAILED') {
                        $commande->update(['statut' => 'annulee']);
                    }
                }
            }
        }

        $commande->refresh();
        return view('client.paiement_statut', compact('commande'));
    }

    public function webhook(Request $request)
    {
        $ref    = $request->input('reference');
        $status = $request->input('status');

        Log::info('Campay webhook received', $request->all());

        if (!$ref || !$status) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $commande = Commande::where('campay_reference', $ref)->first();
        if (!$commande) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        match ($status) {
            'SUCCESSFUL' => $commande->update(['statut' => 'payee']),
            'FAILED'     => $commande->update(['statut' => 'annulee']),
            default      => null,
        };

        return response()->json(['received' => true]);
    }
}
