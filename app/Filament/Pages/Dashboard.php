<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\RencanaKinerjaWidget;
use App\Models\User;
use Filament\Forms\Form;
use App\Models\RencanaKinerja;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Section::make('Filters')->schema([
                Select::make('rencana_kinerja_user.user_id') // Ubah menjadi format relasi yang benar
                    ->label('User')
                    ->placeholder('Pilih User')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->multiple() // Multiple selection allowed
                    ->default(fn () => Auth::check() ? [Auth::id()] : [])
                    ->afterStateUpdated(fn () => $this->refreshCalendar()),

                Select::make('tempat')
                    ->label('Tempat')
                    ->placeholder('Pilih Tempat')
                    ->options([
                        'Ruang Rapat' => 'Ruang Rapat',
                        'PST' => 'PST',
                        'Kantor' => 'Kantor',
                        'Luar Kantor' => 'Luar Kantor',
                        'Online' => 'Online',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->multiple()
                    ->afterStateUpdated(fn () => $this->refreshCalendar()),

                Select::make('kategori')
                    ->label('Kategori')
                    ->placeholder('Pilih Kategori')
                    ->options([
                        'Rapat' => 'Rapat',
                        'Supervisi/Pengawasan' => 'Supervisi/Pengawasan',
                        'Zoom' => 'Zoom',
                        'Pelatihan/Briefing' => 'Pelatihan/Briefing',
                        'SPPD' => 'SPPD',
                        'Cuti' => 'Cuti',
                        'Deadline' => 'Deadline',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->multiple()
                    ->afterStateUpdated(fn () => $this->refreshCalendar()),
            ])->columns(3),
        ]);
    }

    // Method to get filters from the form
    public function getFilters(): array
    {
        Log::info('Filters Applied:', $this->filters);
        return $this->filters; // This will return the filters defined in the page
    }

    public ?string $start = null;
    public ?string $end = null;

    public function mount()
    {
        // Set default range tanggal (misalnya, bulan ini)
        $this->start = now()->startOfMonth()->toDateString();
        $this->end = now()->endOfMonth()->toDateString();
    }

    public function refreshCalendar()
    {
        // Trigger event untuk menyegarkan kalender
        $this->dispatch('filament-fullcalendar--refresh');
    }

    public function getWidgets(): array
    {
        return [
            // Add the widgets you want to display on your dashboard
            CalendarWidget::class,
            RencanaKinerjaWidget::class,
            // Custom widgets or other existing widgets can be added here.
        ];
    }

}
