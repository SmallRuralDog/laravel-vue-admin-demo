<?php


namespace App\Admin\Controllers;


use App\Models\DtkSuperCategory;
use Illuminate\Support\Facades\Http;
use SmallRuralDog\Admin\Components\Attrs\SelectOption;
use SmallRuralDog\Admin\Components\Form\InputNumber;
use SmallRuralDog\Admin\Components\Form\Select;
use SmallRuralDog\Admin\Components\Form\Upload;
use SmallRuralDog\Admin\Components\Grid\Avatar;
use SmallRuralDog\Admin\Components\Grid\Image;
use SmallRuralDog\Admin\Controllers\AdminController;
use SmallRuralDog\Admin\Controllers\AdminResource;
use SmallRuralDog\Admin\Form;
use SmallRuralDog\Admin\Grid;

class DtkSuperCategoryController extends AdminController
{
    use Dtk;

    public function grid()
    {
        $grid = new Grid(new DtkSuperCategory());

        $grid->defaultSort("cid","asc");

        $grid->quickSearch("cname");

        $grid->column('cid', '一级分类ID')->width(150);
        $grid->column('cname', '一级分类名称')->width(150);
        $grid->column('cpic', '一级分类图标')->component(Image::make()->preview()->size(30, 30))->width(150)->help("图片展现模式，点击可预览大图");
        $grid->column('subcid', '二级分类Id')->width(150);
        $grid->column('subcname', '二级分类名称')->width(150);
        $grid->column('scpic', '二级分类图标')->component(Avatar::make())->width(150);

        $grid->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->addRight(Grid\Tools\ToolButton::make("初始化分类")->type("text")->handler('request')->uri(route('DtkSuperCategoryCollect')));
            $toolbars->addRight(Grid\Tools\ToolButton::make("本演示源代码")->type("text")->handler('link')->uri('https://github.com/SmallRuralDog/laravel-vue-admin-demo/blob/master/app/Admin/Controllers/DtkSuperCategoryController.php'));
        });

        return $grid;
    }

    public function form($isEdit = false)
    {
        $form = new Form(new DtkSuperCategory());

        $form->labelWidth("400px");

        $form->item('cid', '一级分类')->component(Select::make()->options(function () {
            return DtkSuperCategory::query()->whereNull('subcid')->get()->map(function ($item) {
                return SelectOption::make($item->cid, $item->cname)->avatar($item->cpic);
            })->all();
        }))->required()->help("select组件，可设置头像");
        $form->item('cname', '一级分类名称')->inputWidth(5)->required();
        $form->item('cpic', '一级分类图标')->inputWidth(5)->required()->component(Upload::make()->avatar()->width(80)->height(80));
        $form->item('subcid', '二级分类Id')->inputWidth(5)->required()->component(InputNumber::make()->min(10000))->help("数字输入框，最小10000");
        $form->item('subcid', '二级分类名称')->inputWidth(5)->required();
        $form->item('scpic', '二级分类图标')->inputWidth(5)->required()->component(Upload::make()->avatar()->width(80)->height(80))->help("上传组件，头像模式 80*80");

        return $form;
    }

    public function collect()
    {
        $host = 'https://openapi.dataoke.com/api/category/get-super-category';
        $data = [
            'appKey' => $this->appKey,
            'version' => '1.1.0',
        ];
        $data['sign'] = $this->makeSign($data);

        $url = $host . '?' . http_build_query($data);

        $data = Http::get($url)->json();

        DtkSuperCategory::query()->delete();


        collect($data['data'])->map(function ($item) {
            $one = DtkSuperCategory::query()->updateOrCreate([
                'cid' => $item['cid']
            ], [
                'cname' => $item['cname'],
                'cpic' => $item['cpic']
            ]);
            collect($item['subcategories'])->map(function ($sub) use ($one) {
                DtkSuperCategory::query()->updateOrCreate([
                    'cid' => $one->cid,
                    'subcid' => $sub['subcid'],
                ], [
                    'cname' => $one->cname,
                    'cpic' => $one->cpic,
                    'subcname' => $sub['subcname'],
                    'scpic' => $sub['scpic'],
                ]);
            });
        });

        return \Admin::responseMessage("同步完成");

    }
}
