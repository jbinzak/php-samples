<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class WP_Cloeve_Contact_List extends WP_List_Table {

    const SINGULAR_NAME = 'Contact';
    const PLURAL_NAME = 'Contacts';
    const TABLE_NAME = 'cloeve_contact';
    const TABLE_DELETE = 'cloeve_contact_delete';

    /** Class constructor */
    public function __construct() {

        parent::__construct( [
            'singular' => __( self::SINGULAR_NAME, 'sp' ), //singular name of the listed records
            'plural'   => __( self::PLURAL_NAME, 'sp' ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ] );

    }

    /**
     * Table create SQL
     * @return string
     */
    public static function retrieveCreateSQL(){

        // wp db
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        return "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message longtext COLLATE utf8mb4_unicode_ci NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		UNIQUE KEY id (id)
	) $charset_collate;";
    }


    /**
     * Retrieve data from the database
     * @param $id
     * @return mixed
     */
    public static function retrieve_record_by_id($id) {

        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $sql = "SELECT * FROM $table_name WHERE id =" .$id;
        $results = $wpdb->get_results( $sql, 'ARRAY_A' );
        if(count($results) <= 0){
            return false;
        }

        $result = $results[0];
        return self::transform_data($result);
    }


    public static function transform_data($result) {

        return $result;
    }

    /**
     * Retrieve data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @param bool $all
     * @return mixed
     */
    public static function retrieve_data( $per_page = 15, $page_number = 1 , $all = false) {

        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $sql = "SELECT * FROM $table_name";

        $sql .= !empty($_REQUEST['orderby']) ? ' ORDER BY ' . esc_sql($_REQUEST['orderby']) : ' ORDER BY created_at ';
        $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' DESC';

        if($all === false){
            $sql .= " LIMIT $per_page";
            $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        }

        $results = $wpdb->get_results( $sql, 'ARRAY_A' );



        return $results;
    }

    /**
     * @param $new_data
     */
    public static function insert_new_record($new_data){

        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        // set date
        $new_data['created_at'] = gmdate('Y-m-d H:i:s');

        // exec
        $wpdb->insert($table_name, $new_data);
    }


    /**
     * @param $id
     * @param $new_data
     */
    public static function update_record($id, $new_data){

        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        // exec
        $wpdb->update($table_name, $new_data, ['id' => $id]);
    }


    /**
     * Delete a record.
     *
     * @param int $id ID
     */
    public function delete_record( $id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;


        $wpdb->delete(
            $table_name,
            [ 'id' => $id ],
            [ '%d' ]
        );
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public function record_count() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $sql = "SELECT COUNT(*) FROM $table_name";

        return $wpdb->get_var( $sql );
    }


    /** Text displayed when no data is available */
    public function no_items() {
        _e( 'No data available.', 'sp' );
    }


    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'created_at':

                // if no date
                if($item[ $column_name ] == '0000-00-00 00:00:00'){
                    return '';
                }

                $timezone = get_option('timezone_string');
                if(empty($timezone)){
                    $date_time = (new DateTime( $item[ $column_name ]))->format('m/d/Y h:i A');
                }else{
                    $date_time = (new DateTime( $item[ $column_name ], new DateTimeZone('GMT')))->setTimezone(new DateTimeZone($timezone))->format('m/d/Y h:i A');
                }
                return esc_html($date_time);
            default:
                return esc_html($item[ $column_name ]);
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function column_name( $item ) {

        $delete_nonce = wp_create_nonce( self::TABLE_DELETE );

        $title = '<strong>' . $item['name'] . '</strong>';

        $actions = [
            'delete' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        ];

        return $title . $this->row_actions( $actions );
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns() {
        $columns = [
            'cb'      => '<input type="checkbox" />',
            'name'    => __( 'Name', 'sp' ),
            'email'    => __( 'Email', 'sp' ),
            'message' => __( 'Message', 'sp' ),
            'created_at' => __( 'Created At', 'sp' )
        ];

        return $columns;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array( 'name', true ),
            'email' => array( 'email', true ),
            'created_at' => array( 'created_at', true )
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => 'Delete'
        ];

        return $actions;
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = 15;
        $current_page = $this->get_pagenum();
        $total_items  = $this->record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );

        $this->items = $this->retrieve_data( $per_page, $current_page );
    }

    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, self::TABLE_DELETE ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                $this->delete_record( absint( $_GET['id'] ) );
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                $this->delete_record( $id );

            }
        }
    }

}