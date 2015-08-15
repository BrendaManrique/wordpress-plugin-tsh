<?php
/*************
* Prepare interface
*WP_List_Table snippet
**************/
/**
 * List page handler
 *
 * This function renders our custom table
 * Notice how we display message about successfull deletion
 * Actualy this is very easy, and you can add as many features
 * as you want.
 *
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples
 */


    global $wpdb;

    $table = new Employees_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'employee_list_table'), count($_REQUEST['empId'])) . '</p></div>';
    }
    ?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Employees', 'employee_list_table')?> <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=newemployee');?>"><?php _e('Add new', 'employee_list_table')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="employees-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php

/******************************************************


if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * employee_list_table_List_Table class that will display our custom table
 * records in nice table
 */
class Employees_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'employee',
            'plural' => 'employees',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        switch( $column_name ) { 
    		case 'user_id':   return $this->column_name($item);
        	case 'empPosition': return $this->column_empPosition($item);
   			default: return $item[ $column_name ];
   		}

    }

    function column_name($item)
    {	$user_info = get_userdata( $item['user_id']);
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on current page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &employee=2
        $actions = array(
            'edit' => sprintf('<a href="?page=newemployee&empId=%s">%s</a>', $item['empId'], __('Edit', 'employee_list_table')),
            'delete' => sprintf('<a href="?page=%s&action=delete&empId=%s">%s</a>', $_REQUEST['page'], $item['empId'], __('Delete', 'employee_list_table')),
        );

        return sprintf('%s %s',
           $user_info->user_login,
            $this->row_actions($actions)
        );
    }

    /**
     * [OPTIONAL] this is example, how to render specific column
     *
     * method name must be like this: "column_[column_name]"
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_empPosition($item)
    {
        return '<em>' . $item['empPosition'] . '</em>';
    }

    function column_isAdmin($item)
    {
        if($item['isAdmin']==1){
        	return  'Yes' ;
        }else{
        	return  '' ;
        }
        
    }
    function column_isMgr($item)
    {
        if($item['isMgr']==1){
        	return  'Manager' ;
        }else{
        	return  'Employee' ;
        }
        
    }


    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="empId[]" value="%s" />',
            $item['empId']
        );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
           // 'empId' => __('Name', 'employee_list_table'),
            'user_id' => __('Employee', 'employee_list_table'),
            'empPosition' => __('Position', 'employee_list_table'),
            'empHireDate' => __('Date of Hire', 'employee_list_table'),
            'isAdmin' => __('Administrator', 'employee_list_table'),
            'isMgr' => __('Account Type', 'employee_list_table'),
        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'user_id' => array('user_id', true),
            'empPosition' => array('empPosition', false),
            'isAdmin' => array('isAdmin', false),
            'empHireDate' => array('empHireDate', false),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->base_prefix . 'tsh_employees'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['empId']) ? $_REQUEST['empId'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE empId IN($ids)");
            }
        }
    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->base_prefix . 'tsh_employees'; // do not forget about tables prefix

        $per_page = 5; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(empId) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'user_id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}







?>
