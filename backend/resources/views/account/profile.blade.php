@extends('layouts.app', ['title' => 'Профиль — CultBear'])

@section('content')
<style>
    .profile-page-wrap {
        max-width: 820px;
        margin: 0 auto;
        padding: 2.5rem 1rem 3rem;
    }

    .profile-card {
        border: 1px solid #e4e4e7;
        border-radius: 1rem;
        background: #fff;
        padding: 1.5rem;
    }

    .profile-title {
        margin: 0;
        font-size: 1.625rem;
        line-height: 1.2;
        font-weight: 800;
        color: #09090b;
    }

    .profile-subtitle {
        margin: 0.5rem 0 0;
        font-size: 0.875rem;
        line-height: 1.45;
        color: #52525b;
    }

    .profile-alert {
        margin-top: 1rem;
        border-radius: 0.625rem;
        padding: 0.75rem;
        font-size: 0.875rem;
        line-height: 1.35;
    }

    .profile-alert-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .profile-alert-error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    .profile-form {
        margin-top: 1.5rem;
        display: grid;
        gap: 1rem;
    }

    .profile-field {
        display: grid;
        gap: 0.375rem;
    }

    .profile-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #3f3f46;
    }

    .profile-input {
        width: 100%;
        border: 1px solid #d4d4d8;
        border-radius: 0.625rem;
        padding: 0.625rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.25;
        color: #09090b;
        background: #fff;
        outline: none;
    }

    .profile-input:focus {
        border-color: #18181b;
        box-shadow: 0 0 0 1px #18181b;
    }

    .profile-grid-2 {
        display: grid;
        gap: 0.875rem;
    }

    .profile-submit {
        margin-top: 0.25rem;
        width: 100%;
        border: 0;
        border-radius: 0.625rem;
        background: #000;
        color: #fff;
        padding: 0.75rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .profile-submit:hover {
        background: #27272a;
    }

    @media (min-width: 640px) {
        .profile-page-wrap {
            padding-top: 3rem;
        }

        .profile-card {
            padding: 1.75rem;
        }

        .profile-grid-2 {
            grid-template-columns: 1fr 1fr;
        }

        .profile-submit {
            width: auto;
            min-width: 220px;
        }
    }
</style>

<section class="profile-page-wrap">
    <div class="profile-card">
        <h1 class="profile-title">Профиль</h1>
        <p class="profile-subtitle">Измените имя и адрес доставки, чтобы оформление заказа занимало меньше времени.</p>

        @if(session('status'))
            <p class="profile-alert profile-alert-success">{{ session('status') }}</p>
        @endif

        @if($errors->any())
            <p class="profile-alert profile-alert-error">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="/account/profile" class="profile-form">
            @csrf
            @method('PUT')

            <div class="profile-field">
                <label for="name" class="profile-label">Имя</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="profile-input">
            </div>

            <div class="profile-field">
                <label for="phone" class="profile-label">Телефон</label>
                <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+7 (___) ___-__-__" class="profile-input">
            </div>

            <div class="profile-field">
                <label for="address_line" class="profile-label">Адрес доставки</label>
                <input id="address_line" name="address_line" value="{{ old('address_line', $user->address_line) }}" placeholder="Улица, дом, квартира" class="profile-input">
            </div>

            <div class="profile-grid-2">
                <div class="profile-field">
                    <label for="city" class="profile-label">Город</label>
                    <input id="city" name="city" value="{{ old('city', $user->city) }}" class="profile-input">
                </div>

                <div class="profile-field">
                    <label for="postal_code" class="profile-label">Индекс</label>
                    <input id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="profile-input">
                </div>
            </div>

            <button type="submit" class="profile-submit">
                Сохранить изменения
            </button>
        </form>
    </div>
</section>
@endsection
