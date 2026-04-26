<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\TelegramNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SendTelegramOrderNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $orderId)
    {
    }

    public function handle(): void
    {
        $order = Order::query()->with('items')->findOrFail($this->orderId);
        $chatId = (string) config('services.telegram.chat_id');
        $token = (string) config('services.telegram.bot_token');

        $message = "Оплачен заказ {$order->number}\n".
            "ФИО: {$order->customer_name}\n".
            "Телефон: {$order->phone}\n".
            "Адрес: {$order->address_line}\n".
            "Сумма: {$order->total_amount}";

        $log = TelegramNotification::query()->create([
            'order_id' => $order->id,
            'chat_id' => $chatId,
            'message' => $message,
            'status' => 'pending',
        ]);

        if ($chatId === '' || $token === '') {
            $log->update(['status' => 'failed', 'response' => 'Missing Telegram credentials']);

            return;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        $log->update([
            'status' => $response->successful() ? 'sent' : 'failed',
            'response' => json_encode($response->json(), JSON_UNESCAPED_UNICODE),
            'sent_at' => $response->successful() ? now() : null,
        ]);
    }
}
