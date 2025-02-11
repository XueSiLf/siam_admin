<?php


namespace app\plugs\cms\controller;


use app\plugs\cms\model\PlugsCmsArticleModel;
use app\plugs\cms\service\CmsArticleService;
use think\facade\App;
use think\facade\View;

class CmsDefaultTemplateController
{
    /**
     * 文章列表
     * @return string
     * @throws \app\exception\ServiceException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $page   = input('page', 1);
        $limit  = input('limit', 10);
        //文章列表
        $article_list = CmsArticleService::get_article_list($page,$limit,$this->build_where());
        //文章分类
        $category_list = CmsArticleService::get_article_category_list();
        View::assign('category_list',$category_list);
        View::assign('article_list',$article_list);
        return View::fetch('/plugs/cms/template/default/index');
    }

    /**
     * 文章详情
     * @return string
     * @throws \app\exception\ServiceException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function article()
    {
        validate(['article_id'=>'require'],['文章ID不能为空'])->check(input());
        $article_id = input('article_id');
        $article_info = CmsArticleService::get_article_info($article_id);
        //文章列表
        $article_list = CmsArticleService::get_article_list();
        //文章分类
        $category_list = CmsArticleService::get_article_category_list();
        View::assign('category_list', $category_list);
        View::assign('article_list', $article_list);
        View::assign('article_info', $article_info);
        return View::fetch('/plugs/cms/template/default/article');
    }

    /**
     * 获取脚本内容
     * @throws \app\exception\ServiceException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function article_script()
    {
        validate(['article_id'=>'require'],['文章ID不能为空'])->check(input());
        $article_id = input('article_id');
        App::debug(false);
        return CmsArticleService::get_article_script($article_id);
    }

    /**
     * 查询条件构造
     * @param array $where
     * @return array|mixed
     */
    protected function build_where($where = [])
    {
        $category_id = input('category_id', '');
        $search      = input('search', '');
        if (!empty($search)){
            $where[] = ['article_title','like',"%$search%"];
        }
        if (!empty($category_id)){
            $where[] = ['article_category_id','=',$category_id];
        }
        //默认只显示发布状态的文章
        $where[] = ['article_status','=',PlugsCmsArticleModel::ARTICLE_STATUS_PUBLIC];
        return $where;
    }
}