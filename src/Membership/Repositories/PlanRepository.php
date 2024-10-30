<?php
// 

namespace MemberGlut\Core\Membership\Repositories;

use MemberGlut\Core\Base;
use MemberGlut\Core\Membership\Models\Plan\PlanFactory;
use MemberGlut\Core\Membership\Models\ModelInterface;
use MemberGlut\Core\Membership\Models\Plan\PlanEntity;

class PlanRepository extends BaseRepository
{
    protected $table;

    public function __construct()
    {
        $this->table = Base::subscription_plans_db_table();
    }

    /**
     * @param PlanEntity $data
     *
     * @return false|int
     */
    public function add(ModelInterface $data)
    {
        global $wpdb;

        return $wpdb;
    }

    /**
     * @param PlanEntity $data
     *
     * @return false|int
     */
    public function update(ModelInterface $data)
    {
        global $wpdb;


        return $wpdb;
    }

    /**
     * @param $id
     *
     * @return int|false
     */
    public function delete($id)
    {
        global $wpdb;

        return $wpdb;
    }

    /**
     * @param $id
     *
     * @return PlanEntity
     */
    public function retrieve($id)
    {
        global $wpdb;

        return $wpdb;
    }

    /**
     * @param int $limit
     * @param int $current_page
     *
     * @return PlanEntity[]|array
     */
    public function retrieveAll($limit = 0, $current_page = 1)
    {
        global $wpdb;
    
        return [];
    }
    
}