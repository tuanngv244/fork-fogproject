<?php
/**
 * Adds the Game Management menu item.
 *
 * PHP version 5
 *
 * @category AddGameMenuItem
 * @package  FOGProject
 * @author   FOG Team
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Adds the Game Management menu item.
 *
 * @category AddGameMenuItem
 * @package  FOGProject
 * @author   FOG Team
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class AddGameMenuItem extends Hook
{
    /**
     * The name of this hook.
     *
     * @var string
     */
    public $name = 'AddGameMenuItem';
    /**
     * The description of this hook.
     *
     * @var string
     */
    public $description = 'Add menu item for Game Management';
    /**
     * The active flag.
     *
     * @var bool
     */
    public $active = true;
    /**
     * The node this hook enacts with.
     *
     * @var string
     */
    public $node = 'game';
    /**
     * Initializes object.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        self::$HookManager
            ->register(
                'MAIN_MENU_DATA',
                array(
                    $this,
                    'menuData'
                )
            )
            ->register(
                'SUB_MENULINK_DATA',
                array(
                    $this,
                    'addSubMenu'
                )
            )
            ->register(
                'SEARCH_PAGES',
                array(
                    $this,
                    'addSearch'
                )
            )
            ->register(
                'PAGES_WITH_OBJECTS',
                array(
                    $this,
                    'addPageWithObject'
                )
            );
    }
    /**
     * The menu data to change.
     *
     * @param mixed $arguments The arguments to change.
     *
     * @return void
     */
    public function menuData($arguments)
    {
        self::arrayInsertAfter(
            'snapin',
            $arguments['main'],
            $this->node,
            array(
                _('Game Management'),
                'fa fa-gamepad'
            )
        );
    }
    /**
     * Add submenu items
     *
     * @param mixed $arguments The arguments to change.
     *
     * @return void
     */
    public function addSubMenu($arguments)
    {
        if ($arguments['id'] != $this->node) {
            return;
        }
        $arguments['menu'] = array(
            'list' => _('All Games'),
            'add' => _('Add New Game'),
        );
    }
    /**
     * Add to search pages.
     *
     * @param mixed $arguments The arguments to change.
     *
     * @return void
     */
    public function addSearch($arguments)
    {
        array_push($arguments['searchPages'], $this->node);
    }
    /**
     * Add page with object.
     *
     * @param mixed $arguments The arguments to change.
     *
     * @return void
     */
    public function addPageWithObject($arguments)
    {
        array_push($arguments['PagesWithObjects'], $this->node);
    }
}
