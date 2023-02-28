<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Attachment\Attachable;

class Product extends Model
{
    use HasFactory, AsSource, Attachable, Filterable;

    protected $allowedFilters = [
        'id',
        'series_id',
        'brand_id',
        'status',
        'category_id',
        'article',
        'model'
    ];

    protected $allowedSorts = [
        'id',
        'series_id',
        'brand_id',
        'status',
        'category_id',
        'article',
        'model'
    ];

    protected $fillable = [
        'brand_id',
        'old_id',
        'site_id',
        'article',
        'model',
        'category_id',
        'series_id',
        'squere',
        'block_type_id',
        'inner_block_color',
        'model_description',
        'model_features',
        'stock',
        'human_stock',
        'status',
        'producing_country_id',
        'sort',
        'is_in_stock',
        'ric_current',
        'wholesale_price',
        'in_way',
        'slug',
        'is_invertor',
        'status_new'
    ];

    public function videos()
    {
        return $this->belongsToMany(Video::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function producing_country()
    {
        return $this->belongsTo(ProducingCountry::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function blockType()
    {
        return $this->belongsTo(BlockType::class);
    }

    public function getPrefix()
    {
        $productPrefix = $this->category()->get()[0]->product_prefix;
        
        if (!is_null($productPrefix)) return $productPrefix;
    }

    public function getFullTitle()
    {
        return $this->getPrefix() . " " . $this->model . " " . $this->brand->name;
    }

    public function images()
    {
        return $this->attachment('photo');
    }

    public function docs()
    {
        return $this->attachment('documents');
    }

    public function badges()
    {
        return $this->hasMany(ProductBadge::class);
    }

    protected $hidden = [
        "created_at",
        "updated_at",
        "old_id",
        "stock",
        "site_id",
    ];
    
    public function block_type()
    {
        return $this->belongsTo(BlockType::class);
    }

    public function sets()
    {
        $tempArray = ProductSet::where('product_id', $this->id)->get()->toArray();
        $array = [];
        foreach ($tempArray as $arr) {
            $array[] = $arr['set_product_id'];
        }

        return $array;
    }

    public function options_in_sets()
    {
        $tempArray = ProductOptionInSet::where('product_id', $this->id)->get()->toArray();
        $array = [];
        foreach ($tempArray as $arr) {
            $array[] = $arr['set_option_id'];
        }

        return $array;
    }

    public function options_not_in_sets()
    {
        $tempArray = ProductOptionNoInSet::where('product_id', $this->id)->get()->toArray();
        $array = [];
        foreach ($tempArray as $arr) {
            $array[] = $arr['set_option_id'];
        }

        return $array;
    }

    public function getParentTree()
    {
        $tree = [];
        $cat = $this->category;
        $category = $cat->title;
        $category_id = $cat->id;
        $tree[] = $category; 
        while ($category_id !== null)
        {   
            $parent_id = Category::find($category_id)->parent_id;
            if ($parent_id !== null) $category_id = $parent_id;
            if ($parent_id == null) {
                return $tree;
            }
            $catName = Category::find($category_id)->title;
            $tree[] = $catName;
        }
        return $tree;
    }

    public function getParentCategory()
    {
        $category_id = $this->category->id;
        while ($category_id !== null)
        {
            $parent_id = Category::find($category_id)->parent_id;
            if ($parent_id !== null) $category_id = $parent_id;
            if ($parent_id == null) {
                return $category_id;
            }
            if ($category_id == 23 OR $category_id == 26) return $category_id;
        }
    }

    public function properties()
    {
        $property_info = PropertiesToProduct::where('product_id', $this->id)->get();

        $product_properties = [];

        foreach (PropertiesGroup::all() as $properties_group_temp) {
            $product_properties[$properties_group_temp->slug] = [
                "real_properties_group_name" => $properties_group_temp->name,
                "properties" => []
            ];
        }
        
        foreach ($property_info as $property) {
            $property_value = $property->property_value;
            $property_id = $property->property_id;

            $property_main_info = ProductProperty::where('id', $property_id)->get()[0];
            $property_group_info = PropertiesGroup::where('id', $property_main_info->group_id)->get()[0];
            $property_name = $property_main_info->name;
            
            $product_properties[$property_group_info->slug]["properties"][] = [
                "property_id" => $property_id,
                "property_units" => $property_main_info->units,
                "property_name" => $property_name,
                "property_value" => $property_value
            ];
        }
    
        return $product_properties;
    }

    public function getMetaTitle()
    {
        //По мета тегам только на товары: вр. поставить- "конечный род + бренд + модель" - купить оптом в Хиконикс
        return implode(" ", [$this->getPrefix(), $this->brand->name, $this->model]);
    }

    public function getMetaDescription()
    {
        // Дисрипт:   Купить + бренд + модель + оптом и заказать по низкой цене с доставкой от крупного поставщика Хиконикс с 25 летним опытом. Климатическая техника, гарантированы скидки, оптовые цены, авторизация в регионах.
        return implode(" ", ["Купить", $this->brand->name, $this->model, "оптом и заказать по низкой цене с доставкой от крупного поставщика Хиконикс с 25 летним опытом. Климатическая техника, гарантированы скидки, оптовые цены, авторизация в регионах."]);
    }

    public function getMetaKeywords()
    {
        // Кейворд: бренд + модель
        return implode(" ", [$this->brand->name, $this->model]);
    }
}
