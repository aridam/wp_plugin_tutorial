<?php
/**
 * Plugin Name: WordPress Plugin Tutorial
 * Plugin URI: https://github.com/chwnam/wp_plugin_tutorial
 * Description: 워드프레스 플러그인 제작을 위한 간단한 튜토리얼
 * Version: 1.0
 * Author: Changwoo Nam
 * Author URI: mailto://cs.chwnam@gmail.com
 * Text Domain: wp_plugin_tutorial
 * License: PUBLIC
 *
 * @see http://codex.wordpress.org/Writing_a_Plugin#File_Headers
 */

/**
 * 플러그인 기능 소개
 * ===============
 * 플러그인은 메인 페이지 아래 2개의 하위 메뉴를 가집니다.
 *   메인 페이지는 그냥 Hello, World를 출력하고 맙니다.
 *   하위 메뉴 1번에는 텍스트 상자 같은 위젯을 만들고 POST 폼 전송을 합니다.
 *   하위 메뉴 2번에는 1번과 비슷하지만 AJAX를 통해 콜을 하는 메뉴를 만들어 봅니다.
 * 플러그인은 또한 옵션 메뉴를 만듭니다.
 *   간단하게 단 1개의 옵션을 받습니다. 이것은 문자열로서 1번이나 2번 하위 메뉴에서 사용됩니다.
 *   옵션값은 플러그인이 활성화되면 자동으로 활성화시킨 유저의 display_name 항목으로 업데이트됩니다.
 *   옵션값은 플러그인이 비활성화되면 해당 항목을 DB에서 아예 삭제하여 초기화합니다.
 * 플러그인은 특별하게 어떤 페이지를 생성합니다.
 *   이 페이지는 특별히 어디에 노출되어 있지 않아서, 직접 다른 페이지가 링크를 걸어 주든지, 아니면 사용자가 명시적으로 URL을 타이핑해야 합니다.
 *   페이지의 콘텐츠는 그냥 Hello, World를 출력하고 맙니다.
 * 플러그인은 커스텀 포스트 타입을 하나 생성합니다.
 *   이 커스텀 포스트 타입은 기본 포스트처럼 대시보드에서 작성 가능합니다.
 *   몇몇 메타 필드들이 표시됩니다.
 */

$main_file = __FILE__;

/**
 * 플러그인이 활성화 될 때의 액션입니다.
 * @link http://codex.wordpress.org/Function_Reference/register_activation_hook
 */
register_activation_hook( $main_file, 'wp_plugin_tutorial_on_activated' );

/**
 * register_activation_hook callback
 */
function wp_plugin_tutorial_on_activated() {

	/**
	 * @link http://codex.wordpress.org/Function_Reference/update_option
	 * @link http://codex.wordpress.org/Function_Reference/get_currentuserinfo
	 */
	update_option( 'wp_plugin_tutorial_option_name', wp_get_current_user()->display_name );
}

/**
 * 플러그인이 비활성화 될 때의 액션입니다.
 * @see http://codex.wordpress.org/Function_Reference/register_deactivation_hook
 */
register_deactivation_hook( $main_file, 'wp_plugin_tutorial_on_deactivated' );

/**
 * register_deactivation_hook callback
 */
function wp_plugin_tutorial_on_deactivated() {

	/**
	 * @link http://codex.wordpress.org/Function_Reference/get_option
	 */
	if( FALSE !== get_option( 'wp_plugin_tutorial_option_name', FALSE ) ) {
		/**
		 * @link http://codex.wordpress.org/Function_Reference/delete_option
		 */
		delete_option( 'wp_plugin_tutorial_option_name' );
	}
}

/**
 * 플러그인이 완전히 삭제될 때의 액션입니다.
 * @see http://codex.wordpress.org/Function_Reference/register_uninstall_hook
 */
register_uninstall_hook( $main_file, 'wp_plugin_tutorial_on_uninstall' );

/**
 * register_uninstall_hook callback
 */
function wp_plugin_tutorial_on_uninstall() {
	// uninstall code ...
}

/**
 * 플러그인의 메뉴를 삽입합니다. 옵션 페이지를 삽입합니다.
 * @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
 */
add_action( 'admin_menu', 'wp_plugin_tutorial_add_admin_menu' );

/**
 * admin_menu action callback
 * 워드프레스 대시보드 관리 메뉴에 이 플러그인의 메뉴를 출력.
 *
 * 구조
 * ====
 * 플러그인 메인 메뉴
 *   > 플러그인 메인 메뉴
 *   > 하위 메뉴 #1
 *   > 하위 메뉴 #2
 * 설정 메뉴 (기존의 워드프레스 기본 메뉴)
 *   > 우리 플러그인의 설정 메뉴
 */
function wp_plugin_tutorial_add_admin_menu() {

	/**
	 * 메인 메뉴 페이지를 삽입
	 * @link http://codex.wordpress.org/Function_Reference/add_menu_page
	 */
	add_menu_page(
		__('WordPress Plugin Tutorial', 'wp_plugin_tutorial' ),     // 웹브라우저 상단에 보일 문자 (<title> 태그)
		__('WordPress Plugin Tutorial', 'wp_plugin_tutorial' ),     // 메뉴 페이지에 보일 문자
		'manage_options',                                           // 플러그인 접근 권한
		'wp_plugin_tutorial_main_menu',                             // 메뉴 슬러그
		'wp_plugin_tutorial_main_menu_callback',                    // 콜백 함수
	    '',                                                         // (옵션) 아이콘 URL
	    NULL                                                        // (옵션) 메뉴 출력 위치
	);

	/**
	 * 두 개의 하위 메뉴를 삽입
	 * @link http://codex.wordpress.org/Function_Reference/add_submenu_page
	 */
	add_submenu_page(
		'wp_plugin_tutorial_main_menu',                             // 부모 페이지의 슬러그
		__('Submenu #1', 'wp_plugin_tutorial'),                     // 웹브라우저 상단에 보일 문자 (<title> 태그)
		__('Submenu #1', 'wp_plugin_tutorial'),                     // 메뉴 페이지에 보일 문자
		'manage_options',                                           // 접근 권한
		'wp_plugin_tutorial_submenu_1',                             // 이 하위 메뉴의 슬러그
		'wp_plugin_tutorial_submenu_1_callback'                     // 콜백 함수
	);

	add_submenu_page(
		'wp_plugin_tutorial_main_menu',
		__('Submenu #2', 'wp_plugin_tutorial'),
		__('Submenu #2', 'wp_plugin_tutorial'),
		'manage_options',
		'wp_plugin_tutorial_submenu_2',
		'wp_plugin_tutorial_submenu_2_callback'
	);

	/**
	 * 메인 메뉴 밑에 바로 하위 메뉴로 반복되는 첫 번째 항목을 삭제합니다.
	 * @link http://codex.wordpress.org/Function_Reference/remove_submenu_page
	 */
	// remove_submenu_page( 'wp_plugin_tutorial_main_menu', 'wp_plugin_tutorial_main_menu' );

	/**
	 * 설정 페이지를 삽입
	 * @link http://codex.wordpress.org/Function_Reference/add_options_page
	 */
	add_options_page(
		__( 'Tutorial Option', 'wp_plugin_tutorial' ),              // 웹브라우저 상단에 보일 문자 (<title> 태그)
		__( 'Tutorial Option', 'wp_plugin_tutorial' ),              // 메뉴 페이지에 보일 문자
		'manage_options',                                           // 접근 권한
		'wp_plugin_tutorial_option',                                // 슬러그
		'wp_plugin_tutorial_option_callback'                        // 콜백 함수
	);
}

/**
 * 옵션 메뉴를 구축합니다.
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
 */
add_action( 'admin_init', 'wp_plugin_tutorial_settings' );
function wp_plugin_tutorial_settings() {

	/**
	 * 옵션을 등록
	 * @link http://codex.wordpress.org/Function_Reference/register_setting
	 * @link http://codex.wordpress.org/Data_Validation
	 */
	register_setting(
		'wp_plugin_tutorial_option_group',         // 옵션 그룹
		'wp_plugin_tutorial_option_name',          // 옵션 이름. DB 테이블의 옵션 이름이 된다.
		'sanitize_text_field'                      // 값 세정을 위한 콜백
	);

	/**
	 * 섹션을 등록
	 * @link http://codex.wordpress.org/Function_Reference/add_settings_section
	 */
	add_settings_section(
		'wp_plugin_tutorial_section',                                           // id
		__( 'Tutorial Plugin Section', 'wp_plugin_tutorial_settings' ),         // 섹션 제목
		'wp_plugin_tutorial_section_callback',                                  // 콜백
		'wp_plugin_tutorial_option'                                             // 이 섹션이 보여질 페이지 슬러그
	);

	/**
	 * 필드를 등록
	 * @link http://codex.wordpress.org/Function_Reference/add_settings_field
	 */
	add_settings_field(
		'wp_plugin_tutorial_field',                                             // id
		__( 'Tutorial Plugin Field', 'wp_plugin_tutorial_settings' ),           // 필드 제목
		'wp_plugin_tutorial_field_callback',                                    // 콜백
		'wp_plugin_tutorial_option',                                            // 이 필드가 속한 페이지 슬러그
		'wp_plugin_tutorial_section',                                           // 이 필드가 속한 섹션 id
		array(
			'id'          =>  'wp_plugin_tutorial_field',        // 이 id는 첫번째 함수 인자와 동일해야 한다.
			'name'        =>  'wp_plugin_tutorial_option_name',  // 이 name DB 테이블 옵션 이름과 동일해야 한다.
			'value'       =>  esc_attr( get_option( 'wp_plugin_tutorial_option_name' ) ),
			'description' =>  '데이터베이스에 입력할 값.'
		)                                                                 // 콜백 함수로 들어가는 인자 목록을 키/값으로
	);
}

/**
 * 플러그인 로컬라이즈를 준비합니다.
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference/plugins_loaded
 */
add_action( 'plugins_loaded', 'wp_plugin_tutorial_localize' );
function wp_plugin_tutorial_localize() {
}

/**
 * 하위 메뉴 #1에서 전송하는 POST 전송에 대한 대응을 준비합니다.
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference/admin_post_%28action%29
 */
add_action( 'admin_post_greet_repeat', 'wp_plugin_tutorial_greet_repeat_callback' );

/**
 * 하위 메뉴 #2에서 AJAX 방식 호출에 대한 대응을 준비합니다.
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_%28action%29
 */
add_action( 'wp_ajax_request_code', 'wp_plugin_tutorial_request_code_callback' );


/**
 * 워드프레스에서 rewrite 하도록 규칙을 설정합니다.
 * 서버에서 rewrite가 허용되어야 합니다.
 */
add_action( 'init', 'wp_plugin_tutorial_rewrite_rule', 10, 0);
function wp_plugin_tutorial_rewrite_rule() {

	/**
	 * @link http://codex.wordpress.org/Rewrite_API/add_rewrite_rule
	 */
	add_rewrite_rule( '^tutorial/([^&]+)/?$',  'index.php?tutorial=$matches[1]', 'top' );
}

/**
 * 특정 쿼리 변수를 허용하도록 필터를 걸어 줍니다.
 * wp_plugin_tutorial 이라는 쿼리 변수를 허용하도록 추가합니다.
 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
 */
add_filter( 'query_vars', 'wp_plugin_tutorial_custom_var');
function wp_plugin_tutorial_custom_var( $vars ) {
	$vars[] = 'tutorial';
	return $vars;
}

/**
 * 쿼리 파싱할 때 원하는 동작이 일어나도록 액션을 겁니다.
 * query_vars 필터를 통해 wp_plugin_tutorial 쿼리 변수를 받도록 허용했으므로, 쿼리 변수에 숫자 값이 받아질 것입니다.
 */
add_action( 'parse_request', 'wp_plugin_tutorial_parse_request' );
function wp_plugin_tutorial_parse_request() {

	global $wp;

	$tutorial = $wp->query_vars['tutorial'];
	if(!empty( $tutorial )) {
		add_action( 'template_redirect', 'wp_plugin_tutorial_template_redirect_callback' );
	}
}

/**
 * 커스텀 포스트를 등록합니다.
 */
add_action( 'init', 'wp_plugin_tutorial_custom_post'  );
function wp_plugin_tutorial_custom_post() {

	/**
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	$labels = array(
		'name'                   => _x( '*Plugin Tutorials*', 'post tutorial', 'wp_plugin_tutorial' ),  // 보통 복수의 이름. 전체 목록 화면 가장 상단에 출력.
		'singular_name'          => _x( 'Plugin Tutorial', 'post singular', 'wp_plugin_tutorial' ),     // 단수 이름
		'menu_name'              => _x( '[Tutorials]', 'admin menu', 'wp_plugin_tutorial' ),            // 메뉴에 나타날 텍스트
		'name_admin_bar'         => _x( 'Tutorial', 'add new on admin bar', 'wp_plugin_tutorial' ),     // Add New 드롭다운 어드민 바에 나오는 내용
		'add_new'                => _x( '[Add New]', 'tutorial', 'wp_plugin_tutorial' ),                // 새 아이템 추가 텍스트
		'add_new_item'           => __( '/Add New/', 'wp_plugin_tutorial' ),                            // 새 아이템 추가 텍스트 (작성 화면에서 출력)
		'new_item'               => __( 'New Tutorial', 'wp_plugin_tutorial' ),                         // 새 튜토리얼 추가
		'all_items'              => __( 'All Tutorials', 'wp_plugin_tutorial' ),                        // 모든 아이템
		'edit_item'              => __( 'Edit Tutorial', 'wp_plugin_tutorial' ),                        // 아이템 수정 (수정 화면에서 화면 상단에 출력)
		'view_item'              => __( 'View Tutorial', 'wp_plugin_tutorial' ),                        // 아이템 조회 텍스트
		'search_items'           => __( '!Search Tutorial', 'wp_plugin_tutorial' ),                     // 아이템 검색 텍스트
		'not_found'              => __( 'No tutorial found ', 'wp_plugin_tutorial' ),                   // 찾지 못함 텍스트
		'not_found_in_trash'     => __( 'No tutorial found in Trash', 'wp_plugin_tutorial' ),           // 휴지통에서 찾지 못함
		'parent_item_colon'      => __( 'Parent Tutorial:', 'wp_plugin_tutorial' ),                     // 부모 페이지를 의미. 있을 경우에만.
	);

	$args = array(
		'labels'                => $labels,
		'description'           => 'tutorial custom post type',         // 설명
		'public'                => TRUE,                                // 외부로 보이는 타입인지
		'exclude_from_search'   => FALSE,                               // 검색 차단할지를 설정
		'publicly_queryable'    => TRUE,                                // 이 타입을 넣은 쿼리를 UI에서 사용할 수 있는지 결정
		'show_ui'               => TRUE,                                // 이 타입을 UI에서 볼 수 있는지 결정
		'show_in_nav_menus'     => TRUE,                                // 네비게이션 메뉴에서 볼 수 있는지 결정
		'show_in_menu'          => TRUE,                                // 어드민 메뉴를 보여줄지 결정
		'show_in_admin_bar'     => TRUE,                                // 어드민 바에서 보여줄 지 결정
		'query_var'             => TRUE,
		'rewrite'               => array( 'slug' => 'tutorial' ),
		'capability_type'       => 'post',
		'has_archive'           => TRUE,
		'hierarchical'          => FALSE,
		'menu_position'         => NULL,
		'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
		'menu_icon'             => NULL,                                // 메뉴 아이콘
		//'capabilities'        => array(),
		'register_meta_box_cb'  => 'wp_plugin_tutorial_meta_box_cb_callback',
		'taxonomies'            => array( 'post_tag', 'category', ),    // 포스트의 기본 태그와 카테고리를 활용
	);

	$obj = register_post_type( 'wpp_tutorial_type', $args );
	if( is_wp_error( $obj ) ) {
		echo $obj->get_error_message();
	}
}

/**
 * 메타 값을 저장하기 위해 액션을 추가합니다.
 * 자세한 사항은 코덱스를 참조하기 바랍니다.
 */
add_action( 'save_post', 'wp_plugin_tutorial_save_post');
function wp_plugin_tutorial_save_post( $post_id ) {

	// save code see http://codex.wordpress.org/Function_Reference/add_meta_box#Examples
}

// 나머지 콜백 함수는 이 쪽에서 구현합니다.
include_once( 'callbacks.php' );