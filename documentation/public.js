/** 
 * @api {get} /products/ Все товары
 * @apiName GetProducts
 * @apiGroup Products
 * @apiVersion 1.0.0
 * 
 * @apiParam {Integer} per_page Количество выводимых на страницу товаров (может не передаваться)
 * 
 * @apiSuccess (200) {Array} data Массив с объектами товаров
*/
/** 
 * @api {get} /products/:id Информация о товаре
 * @apiName GetProduct
 * @apiGroup Products
 * @apiVersion 1.0.0
 * 
 * @apiParam {Integer} id <code>id</code> товара
 * 
 * @apiSuccess (200) {Integer} id
 * @apiSuccess (200) {Integer} brand_id <code>id</code> бренда товара
 * @apiSuccess (200) {String} article Артикул
 * @apiSuccess (200) {String} model Модель
 * @apiSuccess (200) {Ingeger} category_id <code>id</code> категории
 * @apiSuccess (200) {Integer} series_id <code>id</code> серии
 * @apiSuccess (200) {Integer} squere Площадь работы
 * @apiSuccess (200) {Integer} block_type_id <code>id</code> типа блока
 * @apiSuccess (200) {String} inner_block_color Цвет внутреннего блока
 * @apiSuccess (200) {String} model_description Описание модели
 * @apiSuccess (200) {String} model_features Преимущества модели
 * @apiSuccess (200) {String} status Статус: <br><code>not_avaible</code> - товар не доступен к заказу<br><code>avaible</code> - товар актуальный<br><code>on_order</code> - товар под заказ
 * @apiSuccess (200) {Integer} producing_country_id <code>id</code> страна производитель
 * @apiSuccess (200) {Boolean} is_in_stock Информирует о наличии на складе
 * @apiSuccess (200) {String} human_stock Человеко-читаемый остаток
 * @apiSuccess (200) {String} ric_old Старая цена (если есть)
 * @apiSuccess (200) {String} ric_current Текущая цена
 * @apiSuccess (200) {String} wholesale_price Диллерская цена
 * @apiSuccess (200) {Integer} sort Информация о сортировке
 * 
 * @apiSuccess (200) {Object} properties Характеристики
 * @apiSuccess (200) {Object} properties.osnovnye_harakteristiki
 * @apiSuccess (200) {Object} properties.proizvoditielnost_i_potrieblieniie
 * @apiSuccess (200) {Object} properties.eliektropitaniie
 * @apiSuccess (200) {Object} properties.montazhnyie_kharaktieristiki
 * @apiSuccess (200) {Object} properties.loghistichieskiie_kharaktieristiki
 * 
 * @apiSuccess (200) {Object} properties.anygroup Для удобства вместо наименования группы характеристик указано "anygroup", каждая группа характеристик имеет абсолютно одинаковые свойства и различается только названием.
 * @apiSuccess (200) {String} properties.anygroup.real_properties_group_name Человеко-читаемое наименование группы (ru)
 * @apiSuccess (200) {Array} properties.anygroup.properties Массив объектов характеристик
 * @apiSuccess (200) {Integer} properties.anygroup.properties.property_id <code>id</code> характеристики
 * @apiSuccess (200) {String} properties.anygroup.properties.property_units Еденицы измерения (если есть)
 * @apiSuccess (200) {String} properties.anygroup.properties.property_name Название характеристики
 * @apiSuccess (200) {String} properties.anygroup.properties.property_value Значение характеристики
 * 
 * @apiSuccess (200) {Array} images[]
 * @apiSuccess (200) {Array} videos[]
 * @apiSuccess (200) {Array} documents[]
 * 
 * @apiSuccess (200) {Array} anyattach[] Для удобства вместо названия массива вложений указано "anyattach", каждая группа характеристик имеет абсолютно одинаковые свойства и различается только названием.
 * @apiSuccess (200) {Integer} anyattach.id <code>id</code> вложения
 * @apiSuccess (200) {String} anyattach.name Наименование вложения 
 * @apiSuccess (200) {String} anyattach.original_name Человеко-читаемое наименование
 * @apiSuccess (200) {String} anyattach.mime MIME-тип
 * @apiSuccess (200) {String} anyattach.extension Расширение файла
 * @apiSuccess (200) {Integer} anyattach.size Размер файла
 * @apiSuccess (200) {Integer} anyattach.sort Порядок сортировки вложений
 * @apiSuccess (200) {String} anyattach.path Путь из директории хранилища
 * @apiSuccess (200) {String} anyattach.description Описание вложения
 * @apiSuccess (200) {String} anyattach.alt Альтернативное описание
 * @apiSuccess (200) {String} anyattach.hash Хэш файла-вложения
 * @apiSuccess (200) {String} anyattach.url Полный URL к файлу-вложению
 * @apiSuccess (200) {String} anyattach.relativeUrl Относительный url
 * 
 * 
 * @apiSuccess (200) {Object} category Объект категории в которой находится товар
 * 
 * @apiSuccess (200) {Object} brand Объект бренда к которому относится товар
 * 
 * @apiSuccess (200) {Object} country Объект страны-производителя
 * 
 * @apiSuccess (200) {Object} series Объект серии товара
 * 
 * 
*/
/** 
 * @api {get} /products/:id/sets Комплекты
 * @apiDescription Если необходимо получить все комплекты, в которые входит данный товар
 * @apiName GetProductSets
 * @apiGroup Products
 * @apiVersion 1.0.0
 * 
 * @apiParam {Integer} id <code>id</code> товара
 * 
 * @apiSuccess (200) {Array} sets Комплекты товара
 * @apiSuccess (200) {Array} option_in_sets Опции в комплекте
 * @apiSuccess (200) {Array} option_not_in_sets Опции НЕ в комлпекте
*/
/** 
 * @api {get} /brands/ Все бренды
 * @apiName GetBrands
 * @apiGroup Brands
 * @apiVersion 1.0.0
 * 
 * @apiParam {Integer} per_page Количество выводимых на страницу брендов (может не передаваться)
 * 
 * @apiSuccess (200) {Array} data Массив с объектами брендов
*/
/** 
 * @api {get} /brands/:id Информация о бренде
 * @apiName GetBrand
 * @apiGroup Brands
 * @apiVersion 1.0.0
 * 
 * @apiParam {Integer} id <code>id</code> бренда
 * @apiParam {String} name Название бренда
*/
/** 
 * @api {get} /categories/ Все категории
 * @apiName GetCategories
 * @apiGroup Categories
 * @apiVersion 1.0.0
 * 
 * @apiParam {Integer} per_page Количество выводимых на страницу категорий (может не передаваться)
 * 
 * @apiSuccess (200) {Array} data Массив с объектами категорий
 * 
*/
/** 
 * @api {get} /categories/:id Информация о категории
 * @apiName GetCategory
 * @apiGroup Categories
 * @apiVersion 1.0.0
 * 
 * @apiSuccess (200) {Integer} id <code>id</code> категории
 * @apiSuccess (200) {String} title Название категории
 * @apiSuccess (200) {Integer} parent_id <code>id</code> родительской категории
 * @apiSuccess (200) {String} link Ссылка относительно корня сайта
 * @apiSuccess (200) {Boolean} is_archive Находится ли категория в архиве
 * @apiSuccess (200) {Boolean} is_active Активна ли категория
 * @apiSuccess (200) {Boolean} is_private Приватна ли категория
 * @apiSuccess (200) {String} html_description_header Описание для шапки в формате html
 * @apiSuccess (200) {String} html_description_footer Описание для подвала в формате html
 * @apiSuccess (200) {String} html_description_footer_second Описание для подвала в формате html #2
 * @apiSuccess (200) {String} img_preview_path Ссылка на изображение превью категории
 * @apiSuccess (200) {String} filter_string Наименования в хк / фильтр / уточнить
 * @apiSuccess (200) {String} product_prefix Префикс для товаров, находящихся в этой категории
 * @apiSuccess (200) {String} meta_title Мета тег со значением заголовка страницы <code>title</code>
 * @apiSuccess (200) {String} meta_description Мета тег со значением описания страницы <code>description</code>
 * @apiSuccess (200) {String} meta_keys Мета тег со значением ключевых слов страницы <code>keywords</code>
*/
/** 
 * @api {get} /series/ Все серии
 * @apiName GetSeries
 * @apiGroup Series
 * @apiVersion 1.0.0
 * 
 * @apiParam {Integer} per_page Количество выводимых на страницу серий (может не передаваться)
 * 
 * @apiSuccess (200) {Array} data Массив с объектами серий
*/
/** 
 * @api {get} /series/:id Информация о серии
 * @apiName GetOneSeries
 * @apiGroup Series
 * @apiVersion 1.0.0
 * 
 * @apiParam {Integer} id <code>id</code> серии
 * 
 * @apiSuccess (200) {Integer} id <code>id</code> серии
 * @apiSuccess (200) {String} title Название
 * @apiSuccess (200) {Integer} brand_id <code>id</code> бренда серии
 * @apiSuccess (200) {String} link Ссылка относительно корня сайта
 * @apiSuccess (200) {Boolean} is_archive Архивность
 * @apiSuccess (200) {Boolean} is_active Активность
 * @apiSuccess (200) {Boolean} is_private Приватность
 * 
 * @apiSuccess (200) {String} html_description_header Описание для шапки в формате html
 * @apiSuccess (200) {String} html_description_footer Описание для подвала в формате html
 * @apiSuccess (200) {String} html_description_footer_second Описание для подвала в формате html #2
 * 
 * @apiSuccess (200) {String} img_preview_path Ссылка на изображение превью
 * @apiSuccess (200) {String} h1_content Заголовок
 * @apiSuccess (200) {String} html_description Описание серии
 * @apiSuccess (200) {String} html_features Преимущества серии
 * @apiSuccess (200) {Object} brand Объекта бренда серии
 * @apiSuccess (200) {Integer} brand.id <code>id</code> бренда серии
 * @apiSuccess (200) {Integer} brand.name название бренда серии
*/