<?php
declare (strict_types = 1);

/**
 * FileName: Model.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-06-30 15:11
 */

namespace swf\model;

use swf\facade\Db;
use think\db\Query;

class Model extends \think\Model{

    /**
     * 获取当前模型的数据库查询对象
     * @access public
     * @param array|false $scope 使用的全局查询范围
     * @return Query
     */
    public function db($scope = []): Query
    {
        /** @var Query $query */
        if ($this->queryInstance) {
            $query = $this->queryInstance;
        } else {
            $query = Db::buildQuery($this->connection)
                ->name($this->name . $this->suffix)
                ->pk($this->pk);
        }

        $query->model($this)
            ->json($this->json, $this->jsonAssoc)
            ->setFieldType(array_merge($this->schema, $this->jsonType));

        if (!empty($this->table)) {
            $query->table($this->table . $this->suffix);
        }

        // 软删除
        if (property_exists($this, 'withTrashed') && !$this->withTrashed) {
            $this->withNoTrashed($query);
        }

        // 全局作用域
        if (is_array($scope)) {
            $globalScope = array_diff($this->globalScope, $scope);
            $query->scope($globalScope);
        }

        // 返回当前模型的数据库查询对象
        return $query;
    }
}