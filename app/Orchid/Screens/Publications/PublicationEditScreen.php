<?php

namespace App\Orchid\Screens\Publications;

use App\Models\News;
use App\Models\Video;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Str;

class PublicationEditScreen extends Screen
{
    public $publication;
    /**
     * Query data.
     *
     * @return array
     */
    public function query(News $publication): iterable
    {
        return [
            "publication" => $publication
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->publication->exists ? 'Редактировать публикацию "' . $this->publication->h1 .'"' : 'Создать публикациию';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Скопировать')
                ->icon('copyright')
                ->method('copyPublication')
                ->confirm('Вы уверены что хотите скопировать публикацию "' . $this->publication->h1 . "\"?"),

            Button::make('Сохранить')
                ->icon('check')
                ->method('createOrUpdate'),
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
            Layout::tabs([
                "Основной контент" => [
                    Layout::rows([
                        Cropper::make('publication.content_img')
                            ->minCanvas(500)
                            ->maxWidth(1000)
                            ->maxHeight(1000),

                        Input::make('publication.h1')
                            ->title('Заголовок H1'),
                        
                        Input::make('publication.code')
                            ->title('Ссылка')
                            ->help('Для автоматической генерации оставьте поле пустым'),

                        CheckBox::make('publication.is_active')
                            ->sendTrueOrFalse()
                            ->title('Активность'),
                        
                        CheckBox::make('publication.show_on_index')
                            ->sendTrueOrFalse()
                            ->title('Показывать на главной?'),
                        
                        DateTimer::make('publication.date')
                            ->format24hr()
                            ->enableTime()
                            ->title('Дата начала активности'),

                        Select::make('publication.group')
                            ->options([
                                "news" => "Новость",
                                "articles" => "Статья",
                                "video" => "Видео",
                                "actions" => "Акция"
                            ])
                            ->title('Группа'),
                        Quill::make('publication.html')
                            ->title('Контент публикации')
                    ])
                ],
                "Анонс" => [
                    Layout::rows([
                        Cropper::make('publication.anons_img')
                            ->minCanvas(500)
                            ->maxWidth(1000)
                            ->maxHeight(1000),

                        TextArea::make('publication.anons_text')
                            ->title('Текст анонса')
                            ->row(10)
                    ])
                ]
            ])
        ];
    }

    public function copyPublication(Request $request, News $publication)
    {
        $newPublication = $publication->replicate();

        $newPublication->h1 = "Копия -- ".$publication->h1;
        $newPublication->code = $publication->code."_copy";

        $newPublication->save();

        return redirect()->route('platform.publication.edit', $newPublication);
    }

    public function createOrUpdate(Request $request, News $publication)
    {
        if (!is_null($request->get('publication'))) {
            $request_data = $request->get('publication');
            if (is_null($request_data['code'])) {
                $request_data['code'] = Str::slug($request_data['h1'],'-','ru');
            }
            $publication->fill($request_data)->save();
        }

        return redirect()->route('platform.publication.list');
    }
}
