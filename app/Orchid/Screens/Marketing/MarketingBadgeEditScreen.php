<?php

namespace App\Orchid\Screens\Marketing;

use App\Models\MarketingBadge;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class MarketingBadgeEditScreen extends Screen
{
    public $badge;
    /**
     * Query data.
     *
     * @return array
     */
    public function query(MarketingBadge $badge): iterable
    {
        $badge->load('attachment');
        return [
            'badge' => $badge
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->badge->exists ? 'Редактировать маркетинговый ярлык' : 'Создать маркетинговый ярлык';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('save')
                ->method('saveOrUpdate')
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('badge.code')
                    ->title('Код ярлыка')
                    ->required()
                    ->help('Может быть любой строкой без пробелов и переносов'),

                Input::make('badge.title')
                    ->title('Наименование')
                    ->required()
                    ->help('Название маркетингового ярлыка'),

                Input::make('badge.font_size')
                    ->title('Размер текста')
                    ->type('number')
                    ->help('Например "14"'),
                    
                Input::make('badge.color')
                    ->type('color')
                    ->title('Цвет текста'),

                Input::make('badge.inner_text')
                    ->title('Текст внутри')
                    ->help('Если не требуется - оставьте пустым'),

                Upload::make('badge.images')
                    ->groups('marketing')
                    ->title('Изображение')
                    ->maxFileSize(5)
                    ->required()
                    ->maxFiles(1)
                    ->media()
                    ->acceptedFiles('image/*')
                    ->help('Для загрузки картинок, доступно: .png, .jpg, .jpeg (до 5 МБ)'),
            ])
        ];
    }

    public function saveOrUpdate(Request $request, MarketingBadge $badge)
    {
        if (!is_null($request->get('badge'))) {
            $badge->fill($request->get('badge'))->save();
            $badge->attachment()->syncWithoutDetaching($request->input('badge.images', []));
        }

        return redirect()->route('platform.marketing.list');
    }
}
