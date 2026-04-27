<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about', $this->sharedData());
    }

    public function delivery(): View
    {
        return view('pages.delivery', $this->sharedData());
    }

    public function contacts(): View
    {
        return view('pages.contacts', $this->sharedData());
    }

    public function privacy(): View
    {
        return view('pages.privacy', $this->sharedData());
    }

    public function personalDataPolicy(): View
    {
        return view('pages.personal-data-policy', $this->sharedData());
    }

    public function terms(): View
    {
        return view('pages.terms', $this->sharedData());
    }

    /** Редирект Точки после успешной оплаты по платёжной ссылке. */
    public function paymentSuccess(): View
    {
        return view('pages.payment-success', $this->sharedData());
    }

    /** Редирект Точки при неуспешной или отменённой оплате. */
    public function paymentFailed(): View
    {
        return view('pages.payment-failed', $this->sharedData());
    }

    private function sharedData(): array
    {
        return [
            'legal' => Setting::getValue('legal_details', [
                'company_name' => 'ИП CultBear',
                'inn' => '',
                'kpp' => '',
                'ogrn' => '',
                'legal_address' => '',
                'postal_address' => '',
                'email' => 'info@cultbear.local',
                'phone' => '+7 (999) 000-00-00',
            ]),
        ];
    }
}
