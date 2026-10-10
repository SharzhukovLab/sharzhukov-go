<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name'  => ['nullable', 'string', 'max:255'],
        ]);

        $existing = NewsletterSubscriber::where('email', $data['email'])->first();

        if ($existing && $existing->status === 'confirmed') {
            return response()->json(['ok' => true, 'message' => 'already_subscribed']);
        }

        $token = NewsletterSubscriber::generateToken();

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $data['email']],
            [
                'name'               => $data['name'] ?? null,
                'status'             => 'pending',
                'confirmation_token' => $token,
                'source'             => 'footer',
                'locale'             => app()->getLocale(),
                'ip_address'         => $request->ip(),
                'confirmed_at'       => null,
                'unsubscribed_at'    => null,
            ]
        );

        $confirmUrl = url('/newsletter/confirm/' . $token);

        Mail::send('emails.newsletter-confirm', [
            'subscriber' => $subscriber,
            'confirmUrl' => $confirmUrl,
        ], function ($message) use ($subscriber) {
            $message->to($subscriber->email)
                ->subject('Confirm your subscription — Sharzhukov\'s Laboratory');
        });

        return response()->json(['ok' => true, 'message' => 'check_email']);
    }

    public function confirm(string $token)
    {
        $subscriber = NewsletterSubscriber::where('confirmation_token', $token)->first();

        if (! $subscriber) {
            return view('newsletter.result', [
                'status'  => 'error',
                'message' => 'Invalid or expired confirmation link.',
            ]);
        }

        $subscriber->confirm();

        return view('newsletter.result', [
            'status'  => 'success',
            'message' => 'Subscription confirmed. Welcome aboard!',
        ]);
    }

    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('confirmation_token', $token)->first();

        if (! $subscriber) {
            return view('newsletter.result', [
                'status'  => 'error',
                'message' => 'Invalid unsubscribe link.',
            ]);
        }

        $subscriber->unsubscribe();

        return view('newsletter.result', [
            'status'  => 'success',
            'message' => 'You have been unsubscribed.',
        ]);
    }
}