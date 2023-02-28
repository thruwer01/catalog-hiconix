<?php

namespace App\Orchid\Screens\Series;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Series;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SeriesEditScreen extends Screen
{
    public $series;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Series $series): iterable
    {
        return [
            "series" => $series
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->series->name;
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->confirm('Вы уверены что хотите удалить серию?'),
            Button::make('Сохранить')
                ->icon('save')
                ->method('save')
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
                Input::make('series.name')
                    ->title('Название серии'),

                Relation::make('series.brand')
                    ->fromModel(Brand::class, 'name')
                    ->title('Бренд'),
                Input::make('series.link')
                    ->title('Ссылка')
                    ->disabled(),
                Input::make('series.h1_content')
                    ->title('Заголовок H1'),
                CheckBox::make('series.status')
                    ->placeholder('Статус (Активно/Архивно)')
                    ->sendTrueOrFalse(),
            ]),
            Layout::rows([
                Quill::make('series.html_description')
                    ->title('Описание серии'),
                Quill::make('series.html_description_other')
                    ->title('Описание серии (прочее)'),
                Quill::make('series.html_features')
                    ->title('Преимущества серии'),
            ])
        ];
    }

    public function save(Request $request, Series $series)
    {
        $series->fill($request->get('series'));
        $series->save();
        Toast::info('Информация о серии успешно обновлена');
    }

    public function remove(Request $request, Series $series)
    {
        if (count(Product::where('series_id', $series->id)->get()) == 0)
        { 
            $series->delete();

            Toast::info('Серия успешно удалена');
    
            return redirect()->route('platform.series.list');
        } else {
            Toast::warning('Серия не может быть удалена, в ней находятся товары!');
        }
    }
}
