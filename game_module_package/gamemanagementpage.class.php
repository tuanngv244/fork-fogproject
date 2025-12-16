<?php
/**
 * Game management page
 *
 * PHP version 5
 *
 * @category GameManagementPage
 * @package  FOGProject
 * @author   Your Name <your.email@example.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Game management page
 *
 * @category GameManagementPage
 * @package  FOGProject
 * @author   Your Name <your.email@example.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class GameManagementPage extends FOGPage
{
    /**
     * The node this page operates off of.
     *
     * @var string
     */
    public $node = 'game';
    
    /**
     * Initializes the game page class.
     *
     * @param string $name the name to pass
     *
     * @return void
     */
    public function __construct($name = '')
    {
        /**
         * The real name not using our name passer.
         */
        $this->name = _('Game Management');
        
        /**
         * Pull in the FOGPage class items.
         */
        parent::__construct($this->name);
        
        /**
         * Get our nicer names.
         */
        global $id;
        global $sub;
        
        /**
         * If the id is set load our sub-side menu.
         */
        if ($id) {
            /**
             * The other sub menu items.
             */
            $this->subMenu = array(
                "$this->linkformat#game-gen" => self::$foglang['General'],
                $this->delformat => self::$foglang['Delete'],
            );
            
            /**
             * The notes for this item.
             */
            $this->notes = array(
                _('Game Name') => $this->obj->get('name'),
                _('Download Path') => $this->obj->get('downloadPath'),
                _('State') => $this->obj->getStateDisplay(),
                _('Size') => $this->obj->getSize(),
                _('Last Update') => $this->obj->get('lastUpdate'),
            );
        }
        
        /**
         * Allow custom hooks/changes to: Submenu data via.
         */
        self::$HookManager
            ->processEvent(
                'SUB_MENULINK_DATA',
                array(
                    'menu' => &$this->menu,
                    'submenu' => &$this->subMenu,
                    'id' => &$this->id,
                    'notes' => &$this->notes,
                    'object' => &$this->obj,
                    'linkformat' => &$this->linkformat,
                    'delformat' => &$this->delformat,
                )
            );
        
        /**
         * The header data for list/search.
         */
        $this->headerData = array(
            '',
            '',
            '<label for="toggler">'
            . '<input type="checkbox" name="toggle-checkbox" '
            . 'class="toggle-checkboxAction" id="toggler"/>'
            . '</label>',
            _('Game ID'),
            _('Game Name'),
            _('State'),
            _('Update'),
            _('Local Update Time'),
            _('Size (MB)'),
            _('Download Path'),
            _('Run Count'),
            _('Last Run Time'),
        );
        
        /**
         * The template for the list/search elements.
         */
        $this->templates = array(
            '${protected}',
            '${enabled}',
            '<label for="game-${id}">'
            . '<input type="checkbox" name="game[]" '
            . 'value="${id}" class="toggle-action" id="game-${id}"/>'
            . '</label>',
            '${id}',
            '<a href="?node='
            . $this->node
            . '&sub=edit&id=${id}" '
            . 'data-toggle="tooltip" data-placement="right" '
            . 'title="'
            . _('Edit')
            . ': ${name}">'
            . '<i class="fa fa-gamepad"></i> ${name}</a>'
            . '<br/>'
            . '<small>${description}</small>',
            '${state}',
            '${lastUpdate}',
            '${localUpdateTime}',
            '${size}',
            '${downloadPath}',
            '${runCount}',
            '${lastRunTime}',
        );
        
        /**
         * The attributes for the table items.
         */
        $this->attributes = array(
            array(
                'width' => 5,
                'class' => 'filter-false'
            ),
            array(
                'width' => 5,
                'class' => 'filter-false'
            ),
            array(
                'width' => 5,
                'class' => 'parser-false filter-false'
            ),
            array(
                'width' => 8,
            ),
            array(),
            array(
                'class' => 'col-xs-1'
            ),
            array(
                'class' => 'col-xs-2'
            ),
            array(
                'class' => 'col-xs-2'
            ),
            array(
                'class' => 'col-xs-1'
            ),
            array(
                'class' => 'col-xs-2'
            ),
            array(
                'class' => 'col-xs-1'
            ),
            array(
                'class' => 'col-xs-2'
            ),
        );
        
        /**
         * Lambda function to return data either by list or search.
         */
        self::$returnData = function (&$Game) {
            /**
             * Store variables
             */
            $id = $Game->id;
            $name = $Game->name;
            $description = $Game->description;
            $downloadPath = $Game->downloadPath;
            $state = $Game->getStateDisplay();
            $lastUpdate = $Game->lastUpdate;
            $localUpdateTime = $Game->localUpdateTime;
            $runCount = $Game->runCount ? $Game->runCount : 0;
            $lastRunTime = $Game->lastRunTime;
            
            /**
             * Format dates
             */
            if (self::validDate($lastUpdate)) {
                $lastUpdate = self::formatTime($lastUpdate, 'Y-m-d H:i:s');
            } else {
                $lastUpdate = _('Never');
            }
            
            if (self::validDate($localUpdateTime)) {
                $localUpdateTime = self::formatTime($localUpdateTime, 'Y-m-d H:i:s');
            } else {
                $localUpdateTime = _('Never');
            }
            
            if (self::validDate($lastRunTime)) {
                $lastRunTime = self::formatTime($lastRunTime, 'Y-m-d H:i:s');
            } else {
                $lastRunTime = _('Never');
            }
            
            /**
             * Format size
             */
            $size = self::formatByteSize($Game->size);
            
            /**
             * Protection icon
             */
            if ($Game->protected < 1) {
                $protected = sprintf(
                    '<i class="fa fa-unlock fa-1x icon hand" '
                    . 'data-toggle="tooltip" data-placement="right" '
                    . 'title="%s"></i>',
                    _('Not protected')
                );
            } else {
                $protected = sprintf(
                    '<i class="fa fa-lock fa-1x icon hand" '
                    . 'data-toggle="tooltip" data-placement="right" '
                    . 'title="%s"></i>',
                    _('Protected')
                );
            }
            
            /**
             * Enabled icon
             */
            if ($Game->isEnabled) {
                $enabled = '<i class="fa fa-check-circle green" '
                    . 'title="'
                    . _('Enabled')
                    . '" data-toggle="tooltip" data-placement="top">'
                    . '</i>';
            } else {
                $enabled = '<i class="fa fa-times-circle red" '
                    . 'title="'
                    . _('Disabled')
                    . '" data-toggle="tooltip" data-placement="top">'
                    . '</i>';
            }
            
            /**
             * Store the data.
             */
            $this->data[] = array(
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'downloadPath' => $downloadPath,
                'state' => $state,
                'lastUpdate' => $lastUpdate,
                'localUpdateTime' => $localUpdateTime,
                'size' => $size,
                'runCount' => $runCount,
                'lastRunTime' => $lastRunTime,
                'protected' => $protected,
                'enabled' => $enabled,
            );
        };
    }
    
    /**
     * Display page.
     *
     * @return void
     */
    public function index()
    {
        $this->title = _('All Games');
        
        /**
         * Attributes
         */
        $this->attributes = array(
            array(),
            array(),
        );
        
        /**
         * Templates
         */
        $this->templates = array(
            '${field}',
            '${input}',
        );
        
        /**
         * Field data
         */
        $fields = array(
            _('Total Games') => self::getClass('GameManager')->count(),
            _('Total Size') => self::formatByteSize(
                self::getClass('GameManager')->getTotalSize()
            ),
            _('Downloaded') => self::getClass('GameManager')
                ->count(array('state' => 2)),
            _('Downloading') => self::getClass('GameManager')
                ->count(array('state' => 1)),
        );
        
        foreach ((array)$fields as $field => &$input) {
            $this->data[] = array(
                'field' => $field,
                'input' => $input,
            );
            unset($input);
        }
        
        /**
         * Output the information.
         */
        $this->render();
        
        unset($this->data);
        
        /**
         * Reset our elements back for list
         */
        $this->resetRequest();
        
        /**
         * Set title
         */
        $this->title = _('All Games');
        
        /**
         * Find items
         */
        $this->data = array();
        
        Route::listem('game');
        
        /**
         * Output the list.
         */
        $this->render();
    }
    
    /**
     * Search page.
     *
     * @return void
     */
    public function search()
    {
        /**
         * Present the search.
         */
        $this->data = array();
        
        Route::searchm('game');
        
        $this->render();
    }
    
    /**
     * Game form display.
     *
     * @return void
     */
    public function gameForm()
    {
        unset(
            $this->data,
            $this->form
        );
        
        $this->attributes = array(
            array(),
            array(),
        );
        
        $this->templates = array(
            '${field}',
            '${input}',
        );
        
        $states = array(
            0 => _('Not Downloaded'),
            1 => _('Downloading'),
            2 => _('Downloaded'),
            3 => _('Installing'),
            4 => _('Installed'),
            5 => _('Updating'),
            6 => _('Error'),
        );
        
        $fields = array(
            '<label for="game-icon">'
            . _('Game Icon')
            . '</label>' => self::getClass('GameManager')
                ->inputText(
                    'icon',
                    $this->obj->get('icon')
                ),
            '<label for="game-name">'
            . _('Game Name')
            . ' *'
            . '</label>' => self::getClass('GameManager')
                ->inputText(
                    'name',
                    $this->obj->get('name')
                ),
            '<label for="game-desc">'
            . _('Description')
            . '</label>' => self::getClass('GameManager')
                ->inputTextArea(
                    'description',
                    $this->obj->get('description')
                ),
            '<label for="game-download-path">'
            . _('Download Path')
            . ' *'
            . '</label>' => self::getClass('GameManager')
                ->inputText(
                    'downloadPath',
                    $this->obj->get('downloadPath')
                ),
            '<label for="game-executable">'
            . _('Executable')
            . '</label>' => self::getClass('GameManager')
                ->inputText(
                    'executable',
                    $this->obj->get('executable')
                ),
            '<label for="game-parameters">'
            . _('Parameters')
            . '</label>' => self::getClass('GameManager')
                ->inputText(
                    'parameters',
                    $this->obj->get('parameters')
                ),
            '<label for="game-archive-path">'
            . _('Archive Path')
            . '</label>' => self::getClass('GameManager')
                ->inputText(
                    'archivePath',
                    $this->obj->get('archivePath')
                ),
            '<label for="game-sync-server">'
            . _('Sync Server')
            . '</label>' => self::getClass('GameManager')
                ->inputText(
                    'syncServer',
                    $this->obj->get('syncServer')
                ),
            '<label for="game-drive-letter">'
            . _('Drive Letter')
            . '</label>' => self::getClass('GameManager')
                ->inputText(
                    'driveLetter',
                    $this->obj->get('driveLetter')
                ),
            '<label for="game-state">'
            . _('State')
            . '</label>' => self::getClass('GameManager')
                ->inputSelect(
                    'state',
                    $states,
                    $this->obj->get('state')
                ),
            '<label for="game-protected">'
            . _('Protected')
            . '</label>' => sprintf(
                '<input type="checkbox" name="protected" id="game-protected"%s/>',
                (
                    $this->obj->get('protected') ?
                    ' checked' :
                    ''
                )
            ),
            '<label for="game-enabled">'
            . _('Enabled')
            . '</label>' => sprintf(
                '<input type="checkbox" name="isEnabled" id="game-enabled"%s/>',
                (
                    $this->obj->get('isEnabled') ?
                    ' checked' :
                    ''
                )
            ),
            '<label for="game-auto-update">'
            . _('Auto Update')
            . '</label>' => sprintf(
                '<input type="checkbox" name="autoUpdate" id="game-auto-update"%s/>',
                (
                    $this->obj->get('autoUpdate') ?
                    ' checked' :
                    ''
                )
            ),
            '<label for="add">'
            . '&nbsp;'
            . '</label>' => sprintf(
                '<input type="submit" value="%s"/>',
                (
                    $this->obj->get('id') ?
                    _('Update') :
                    _('Add')
                )
            ),
        );
        
        foreach ((array)$fields as $field => &$input) {
            $this->data[] = array(
                'field' => $field,
                'input' => $input,
            );
            unset($input);
        }
        
        self::$HookManager
            ->processEvent(
                'GAME_EDIT',
                array(
                    'headerData' => &$this->headerData,
                    'data' => &$this->data,
                    'templates' => &$this->templates,
                    'attributes' => &$this->attributes
                )
            );
        
        echo '<form method="post" action="'
            . $this->formAction
            . '">';
        
        $this->render();
        
        echo '</form>';
    }
    
    /**
     * Add new game.
     *
     * @return void
     */
    public function add()
    {
        $this->title = _('New Game');
        
        unset($this->headerData);
        
        $this->attributes = array(
            array(),
            array(),
        );
        
        $this->templates = array(
            '${field}',
            '${input}',
        );
        
        echo '<div id="tab-container" class="tab-container">';
        echo '<ul class="nav nav-tabs" role="tablist">';
        echo '<li role="presentation" class="active">';
        echo '<a href="#game-gen" aria-controls="game-gen" '
            . 'role="tab" data-toggle="tab">';
        echo _('General');
        echo '</a>';
        echo '</li>';
        echo '</ul>';
        echo '<div class="tab-content">';
        echo '<div role="tabpanel" class="tab-pane active" id="game-gen">';
        
        $this->gameForm();
        
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Add post.
     *
     * @return void
     */
    public function addPost()
    {
        self::$HookManager->processEvent('GAME_ADD_POST');
        
        try {
            $name = trim($_REQUEST['name']);
            if (empty($name)) {
                throw new Exception(_('Game name is required'));
            }
            
            $downloadPath = trim($_REQUEST['downloadPath']);
            if (empty($downloadPath)) {
                throw new Exception(_('Download path is required'));
            }
            
            if (self::getClass('GameManager')->exists($name)) {
                throw new Exception(_('Game name already exists'));
            }
            
            $Game = self::getClass('Game')
                ->set('name', $name)
                ->set('description', trim($_REQUEST['description']))
                ->set('icon', trim($_REQUEST['icon']))
                ->set('downloadPath', $downloadPath)
                ->set('executable', trim($_REQUEST['executable']))
                ->set('parameters', trim($_REQUEST['parameters']))
                ->set('archivePath', trim($_REQUEST['archivePath']))
                ->set('syncServer', trim($_REQUEST['syncServer']))
                ->set('driveLetter', trim($_REQUEST['driveLetter']))
                ->set('state', (int)$_REQUEST['state'])
                ->set('protected', isset($_REQUEST['protected']) ? 1 : 0)
                ->set('isEnabled', isset($_REQUEST['isEnabled']) ? 1 : 0)
                ->set('autoUpdate', isset($_REQUEST['autoUpdate']) ? 1 : 0)
                ->set('createdBy', self::$FOGUser->get('name'));
            
            if (!$Game->save()) {
                throw new Exception(_('Failed to create game'));
            }
            
            $this->setMessage(_('Game created successfully'));
            $this->redirect(
                sprintf(
                    '?node=%s&sub=edit&id=%s',
                    $this->node,
                    $Game->get('id')
                )
            );
        } catch (Exception $e) {
            $this->setMessage($e->getMessage());
            $this->redirect($this->formAction);
        }
    }
    
    /**
     * Edit game.
     *
     * @return void
     */
    public function edit()
    {
        $this->title = sprintf(
            '%s: %s',
            _('Edit'),
            $this->obj->get('name')
        );
        
        unset($this->headerData);
        
        echo '<div id="tab-container" class="tab-container">';
        echo '<ul class="nav nav-tabs" role="tablist">';
        echo '<li role="presentation" class="active">';
        echo '<a href="#game-gen" aria-controls="game-gen" '
            . 'role="tab" data-toggle="tab">';
        echo _('General');
        echo '</a>';
        echo '</li>';
        echo '</ul>';
        echo '<div class="tab-content">';
        echo '<div role="tabpanel" class="tab-pane active" id="game-gen">';
        
        $this->gameForm();
        
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Edit post.
     *
     * @return void
     */
    public function editPost()
    {
        self::$HookManager->processEvent('GAME_EDIT_POST', array('Game' => &$this->obj));
        
        try {
            $name = trim($_REQUEST['name']);
            if (empty($name)) {
                throw new Exception(_('Game name is required'));
            }
            
            $downloadPath = trim($_REQUEST['downloadPath']);
            if (empty($downloadPath)) {
                throw new Exception(_('Download path is required'));
            }
            
            $this->obj
                ->set('name', $name)
                ->set('description', trim($_REQUEST['description']))
                ->set('icon', trim($_REQUEST['icon']))
                ->set('downloadPath', $downloadPath)
                ->set('executable', trim($_REQUEST['executable']))
                ->set('parameters', trim($_REQUEST['parameters']))
                ->set('archivePath', trim($_REQUEST['archivePath']))
                ->set('syncServer', trim($_REQUEST['syncServer']))
                ->set('driveLetter', trim($_REQUEST['driveLetter']))
                ->set('state', (int)$_REQUEST['state'])
                ->set('protected', isset($_REQUEST['protected']) ? 1 : 0)
                ->set('isEnabled', isset($_REQUEST['isEnabled']) ? 1 : 0)
                ->set('autoUpdate', isset($_REQUEST['autoUpdate']) ? 1 : 0);
            
            if (!$this->obj->save()) {
                throw new Exception(_('Failed to update game'));
            }
            
            $this->setMessage(_('Game updated successfully'));
            $this->redirect($this->formAction);
        } catch (Exception $e) {
            $this->setMessage($e->getMessage());
            $this->redirect($this->formAction);
        }
    }
}
