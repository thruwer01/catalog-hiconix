<?php

namespace App\Orchid\Screens\Video;

use App\Models\Video;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;

class VideoEditScreen extends Screen
{
    public $video;
    /**
     * Query data.
     *
     * @return array
     */
    public function query(Video $video): iterable
    {
        return [
            "video" => $video
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->video->exists ? 'Редактировать видео "' . $this->video->title .'"' : 'Добавить новое видео';
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
                Input::make('video.title')
                    ->title('Название видео'),
                TextArea::make('video.description')
                    ->title('Анонс описания'),
                Input::make('video.youtube_url')
                    ->title('Ссылка')
                    ->help('Видео с YouTube')
            ])
        ];
    }

    public function saveOrUpdate(Request $request, Video $video)
    {
        if (!is_null($request->get('video'))) {
            $video->fill($request->get('video'))->save();
        }

        return redirect()->route('platform.video.list');
    }
}
