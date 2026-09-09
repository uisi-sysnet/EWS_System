<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    /**
     * Send a plain text message to the configured Telegram chat.
     * Returns false (and logs) instead of throwing, so a Telegram
     * outage never blocks whatever triggered the notification.
     */
    public function sendMessage(string $text): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            Log::warning('Telegram notification skipped: TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID not configured.');
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id'    => $chatId,
                    'text'       => $text,
                    'parse_mode' => 'HTML',
                ]);

            if (! $response->successful()) {
                Log::error('Telegram sendMessage failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Telegram sendMessage error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check whether the bot can currently reach Telegram with a valid token.
     * Calls the lightweight getMe endpoint rather than sending a message,
     * so this is safe to poll from the dashboard without spamming the chat.
     */
    public function checkConnection(): bool
    {
        $token = config('services.telegram.bot_token');

        if (! $token) {
            return false;
        }

        try {
            $response = Http::timeout(5)->get("https://api.telegram.org/bot{$token}/getMe");

            return $response->successful() && $response->json('ok') === true;
        } catch (\Throwable $e) {
            Log::error('Telegram checkConnection error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a photo (by local file path) with an optional caption.
     * Kept here now so the later "status snapshot image" feature
     * can reuse this same service without another round trip.
     */
    public function sendPhoto(string $filePath, string $caption = ''): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            Log::warning('Telegram photo skipped: TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID not configured.');
            return false;
        }

        try {
            $response = Http::timeout(20)
                ->attach('photo', file_get_contents($filePath), basename($filePath))
                ->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                    'chat_id'    => $chatId,
                    'caption'    => $caption,
                    'parse_mode' => 'HTML',
                ]);

            if (! $response->successful()) {
                Log::error('Telegram sendPhoto failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Telegram sendPhoto error: ' . $e->getMessage());
            return false;
        }
    }
}