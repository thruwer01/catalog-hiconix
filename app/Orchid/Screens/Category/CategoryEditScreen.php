<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Code;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class CategoryEditScreen extends Screen
{
    public $category;
    /**
     * Query data.
     *
     * @return array
     */
    public function query(Category $category): iterable
    {
        return [
            "category" => $category
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->category->title;
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
            Layout::tabs([
                "Основная информация" => [
                    Layout::rows([
                        Input::make('category.title')
                            ->title('Наименование'),
                        Relation::make('category.parent_id')
                            ->fromModel(Category::class, 'title')
                            ->disabled()
                            ->title('Родительская категория')
                            ->help('Редактирование только через excel-таблицу'),
                        Input::make('category.link')
                            ->title('Ссылка'),
                        Input::make('category.sort')
                            ->type('number')
                            ->title('Сортировка')
                            ->help('Чем больше число, тем далее в списке находится объект'),
                        CheckBox::make('category.is_private')
                            ->title('Приватность')
                            ->sendTrueOrFalse(),
                        Input::make('category.filter_string')
                            ->title('Наименования в хк/фильтр/уточнить/см.еще'),
                        Input::make('category.product_prefix')
                            ->title('Префикс')
                            ->help('Префикс для товаров, которые находятся ТОЛЬКО в этой категории')
                    ]),
                    Layout::rows([
                        Quill::make('category.html_description_header')
                            ->title('Описание сверху'),
                        Code::make('category.html_description_header')
                            ->language(Code::MARKUP)
                            ->lineNumbers(),
                        Quill::make('category.html_description_footer')
                            ->title('Описание снизу'),
                        Code::make('category.html_description_footer')
                            ->language(Code::CLIKE),
                    ])->title('Контент')
                ],
                "Управление на сайте hiconix.ru" => [
                    Layout::rows([
                        CheckBox::make('category.is_active_hiconix')
                            ->title('Активность на сайте')
                            ->sendTrueOrFalse(),
                        CheckBox::make('category.is_menu_hiconix')
                            ->title('Отображать в меню на сайте')
                            ->sendTrueOrFalse(),
                        TextArea::make('category.meta_title')
                            ->rows(3),
                        TextArea::make('category.meta_description')
                            ->rows(10),
                        TextArea::make('category.meta_keys')
                            ->rows(3)
                    ])
                ],
            ])
        ];
    }

    public function saveOrUpdate(Request $request, Category $category)
    {
        if (!is_null($request->get('category'))) {
            $category->fill($request->get('category'))->save();
        }

        return redirect()->route('platform.category.list');
    }
}
