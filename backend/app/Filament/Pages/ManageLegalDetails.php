<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ManageLegalDetails extends Page
{
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static string $view = 'filament.pages.manage-legal-details';

    protected static ?string $title = 'Реквизиты и контакты';

    protected static ?string $navigationLabel = 'Реквизиты';

    protected static ?int $navigationSort = 15;

    protected static ?string $slug = 'legal-details';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('manage settings') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill(Setting::getValue('legal_details', $this->defaultLegal()));
    }

    /**
     * @return array<string, string>
     */
    protected function defaultLegal(): array
    {
        return [
            'company_name' => '',
            'inn' => '',
            'kpp' => '',
            'ogrn' => '',
            'legal_address' => '',
            'postal_address' => '',
            'email' => '',
            'phone' => '',
        ];
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @return array<string, Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make('Юридические данные')
                            ->description('Эти поля выводятся в подвале сайта, на странице контактов и в текстах политики конфиденциальности.')
                            ->schema([
                                TextInput::make('company_name')
                                    ->label('Название организации или ИП')
                                    ->maxLength(255),
                                TextInput::make('inn')
                                    ->label('ИНН')
                                    ->maxLength(12),
                                TextInput::make('kpp')
                                    ->label('КПП')
                                    ->maxLength(9)
                                    ->helperText('Для юрлиц. У ИП можно оставить пустым.'),
                                TextInput::make('ogrn')
                                    ->label('ОГРН или ОГРНИП')
                                    ->maxLength(15),
                                Textarea::make('legal_address')
                                    ->label('Юридический адрес')
                                    ->rows(2)
                                    ->maxLength(1000),
                                Textarea::make('postal_address')
                                    ->label('Почтовый адрес')
                                    ->rows(2)
                                    ->maxLength(1000),
                            ])
                            ->columns(2),
                        Section::make('Контакты для покупателей')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('Телефон')
                                    ->tel()
                                    ->maxLength(40),
                            ])
                            ->columns(2),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Сохранить')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::updateOrCreate(
            ['key' => 'legal_details'],
            [
                'group' => 'site',
                'value' => $data,
            ],
        );

        Notification::make()
            ->title('Сохранено')
            ->body('Реквизиты и контакты обновлены на сайте.')
            ->success()
            ->send();
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Здесь задаются ИНН, ОГРН, название компании и контакты — то, что видят посетители внизу страниц.';
    }
}
