<?php

namespace Exceedone\Exment\Controllers;

use Exceedone\Exment\Model\OperationLog;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Arr;

class LogController extends AdminControllerBase
{
    use HasResourceActions;

    public function __construct()
    {
        $this->setPageInfo(trans('admin.operation_log'), trans('admin.operation_log'), exmtrans('operation_log.description'), 'fa-file-text');
    }

    /**
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OperationLog());

        $grid->model()->orderBy('id', 'DESC');

        $grid->column('user.user_name', exmtrans('operation_log.user_name'))->display(function ($foo, $column, $model) {
            return ($model->user ? $model->user->user_name : null);
        });
        $grid->column('method', exmtrans('operation_log.method'));
        $grid->column('path', exmtrans('operation_log.path'));
        $grid->column('ip', exmtrans('operation_log.ip'));
        $grid->column('created_at', trans('admin.created_at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
        });

        $grid->disableCreateButton();
        $grid->disableExport();

        $grid->filter(function (Grid\Filter $filter) {
            $userModel = config('admin.database.users_model');

            $filter->equal('user_id', exmtrans('operation_log.user_name'))->select($userModel::all()->pluck('name', 'id'));
            $filter->equal('method', exmtrans('operation_log.method'))->select(array_combine(OperationLog::$methods, OperationLog::$methods));
            $filter->like('path', exmtrans('operation_log.path'));
            $filter->equal('ip', exmtrans('operation_log.ip'));
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $model = OperationLog::findOrFail($id);
        return new Show($model, function (Show $show) {
            $show->field('user.user_name', exmtrans('operation_log.user_name'))->as(function ($foo, $model) {
                return ($model->user ? $model->user->user_name : null);
            });
            $show->field('method', exmtrans('operation_log.method'));
            $show->field('path', exmtrans('operation_log.path'));
            $show->field('ip', exmtrans('operation_log.ip'));
            $show->field('input', exmtrans('operation_log.input'))->as(function ($input) {
                $input = json_decode($input, true);
                $input = Arr::except($input, ['_pjax', '_token', '_method', '_previous_']);
                if (empty($input)) {
                    return '{}';
                }

                return json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            });
            $show->field('created_at', trans('admin.created_at'));

            $show->panel()->tools(function ($tools) {
                $tools->disableEdit();
            });
        });
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $ids = explode(',', $id);

        if (OperationLog::destroy(array_filter($ids))) {
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }
}
