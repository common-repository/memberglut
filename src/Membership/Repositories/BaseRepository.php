<?php
// 

namespace MemberGlut\Core\Membership\Repositories;

abstract class BaseRepository implements RepositoryInterface
{
    protected $table;

    public function wpdb()
    {
        global $wpdb;

        return $wpdb;
    }

    /**
     * Update a column in table.
     *
     * @param int $id
     * @param string $column
     * @param string $value
     *
     * @return false|int
     */
    public function updateColumn($id, $column, $value)
    {
        global $wpdb;
    
        $table_name = $wpdb->prefix . 'mglut_plans';
    
        $update_data = array(
            $column => $value
        );
    
        $where = array(
            'id' => $id
        );
    
        $format = array(
            '%s'
        );
    
        $where_format = array(
            '%d'
        );
    
        return $wpdb;
    }
    

    /**
     * Retrieve a column in DB table.
     *
     * @param int $id
     * @param string $column
     *
     * @return string|null
     */
    public function retrieveColumn($id, $column)
    {
        global $wpdb;
        
        return $this->wpdb()->get_var(
            $this->wpdb()->prepare(
                "SELECT $column FROM {$wpdb->prefix}mglut_plans WHERE id = %d",
                $id
            )
        );
    }

    /**
     * @return string|null
     */
    public function record_count()
    {
        global $wpdb;
        
        return $this->wpdb()->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mglut_plans");
    }

    /**
     * @return static
     */
    public static function init()
    {
        return new static();
    }
}