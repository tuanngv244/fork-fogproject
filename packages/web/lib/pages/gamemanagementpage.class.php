<?php
/**
 * Game management page
 *
 * PHP version 5
 *
 * @category GameManagementPage
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Game management page
 *
 * @category GameManagementPage
 * @package  FOGProject
 * @author   FOG Project
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
                "$this->linkformat#game-gen" => _('General'),
                $this->delformat => _('Delete'),
            );
            /**
             * The notes for this item.
             */
            $this->notes = array(
                _('Game Name') => $this->obj->get('name'),
                _('Game Path') => $this->obj->get('downloadPath'),
                _('Game Version') => $this->obj->get('version'),
                _('Publisher') => $this->obj->get('publisher') ?: _('Not set'),
                _('Last Updated') => $this->obj->get('lastUpdate'),
            );
        }
        /**
         * Allow custom hooks/changes to: Submenu data via.
         *
         * Menu, submenu, id, notes, the main object,
         * linkformat, delformat, and membership information.
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
                    'membership' => &$this->membership
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
            _('Game Name'),
            _('Storage Group'),
            _('Game Size'),
            _('Last Modified'),
        );
        /**
         * The template for the list/search elements.
         */
        $this->templates = array(
            '${protected}',
            '${enabled}',
            '<label for="toggler1">'
            . '<input type="checkbox" name="game[]" '
            . 'value="${id}" class="toggle-action" id="'
            . 'toggler1"/></label>',
            '<a href="?node='
            . $this->node
            . '&sub=edit&id=${id}" '
            . 'data-toggle="tooltip" data-placement="right" '
            . 'title="'
            . _('Edit')
            . ': ${name}">${name} - ${id}</a>'
            . '<br/>'
            . '<small>${game_type}</small>'
            . '<br/>'
            . '<small>${sync_method}</small>',
            '${storageGroup}',
            '${size}',
            '${modified}',
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
                'width' => 16,
                'class' => 'parser-false filter-false'
            ),
            array(),
            array(
                'class' => 'col-xs-1'
            ),
            array(
                'class' => 'col-xs-1'
            ),
            array('class' => 'col-xs-1')
        );
        /**
         * Lambda function to return data either by list or search.
         *
         * @param object $Game the object to use.
         *
         * @return void
         */
        self::$returnData = function (&$Game) {
            /**
             * Stores the game size.
             */
            $gameSize = self::formatByteSize(
                $Game->size
            );
            /**
             * The id.
             */
            $id = $Game->id;
            /**
             * The name.
             */
            $name = $Game->name;
            /**
             * The description.
             */
            $description = $Game->description;
            /**
             * The modified date.
             */
            $date = $Game->lastUpdate;
            /**
             * If the date is valid format in Y-m-d H:i:s
             * and if not set to no valid data.
             */
            if (self::validDate($date)) {
                $date = self::formatTime($date, 'Y-m-d H:i:s');
            } else {
                $date = _('Never');
            }
            /**
             * The version.
             */
            $version = $Game->version;
            if (!$version) {
                $version = _('Not set');
            }
            /**
             * The publisher.
             */
            $publisher = $Game->publisher;
            if (!$publisher) {
                $publisher = _('Not set');
            }
            /**
             * If the game is enabled or not.
             */
            if ($Game->state == 1) {
                $enabled = '<i class="fa fa-check-circle green" '
                    . 'title="'
                    . _('Enabled')
                    . '" data-toggle="tooltip" data-placement="top">'
                    . '</i>';
            } else {
                $enabled
                    = '<i class="fa fa-times-circle red" '
                    . 'title="'
                    . _('Disabled')
                    . '" data-toggle="tooltip" data-placement="top">'
                    . '</i>';
            }
            
            $protected = '<i class="fa fa-unlock fa-1x icon hand" '
                . 'data-toggle="tooltip" data-placement="right" '
                . 'title="'
                . _('Not protected')
                . '"></i>';
            
            /**
             * Store the data.
             */
            $this->data[] = array(
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'storageGroup' => $publisher,
                'modified' => $date,
                'size' => $gameSize,
                'game_type' => $version,
                'sync_method' => _('Direct'),
                'protected' => $protected,
                'enabled' => $enabled
            );
            /**
             * Cleanup.
             */
            unset(
                $id,
                $name,
                $description,
                $date,
                $gameSize,
                $version,
                $publisher,
                $protected,
                $enabled,
                $Game
            );
        };
    }
    /**
     * The form to display when adding a new game
     * definition.
     *
     * @return void
     */
    public function add()
    {
        /**
         * Setup our variables for back up/incorrect settings without
         * having to lose all the work done previously.
         */
        $storagegroup = (int)filter_input(INPUT_POST, 'storagegroup');
        $gametype = (int)filter_input(INPUT_POST, 'gametype');
        $name = filter_input(INPUT_POST, 'name');
        $desc = filter_input(INPUT_POST, 'description');
        $file = filter_input(INPUT_POST, 'file');
        $version = filter_input(INPUT_POST, 'version');
        $syncmethod = filter_input(INPUT_POST, 'syncmethod');
        /**
         * Set title to display.
         */
        $this->title = _('New Game');
        /**
         * Setup our attributes for rows.
         */
        $this->attributes = array(
            array('class' => 'col-xs-4'),
            array('class' => 'col-xs-8 form-group'),
        );
        /**
         * Setup our templates for rows.
         */
        $this->templates = array(
            '${field}',
            '${input}',
        );
        /**
         * If the storagegroup is > 0, set the group ID to that
         * otherwise try getting the default group.
         */
        if ($storagegroup > 0) {
            $sgID = $storagegroup;
        } else {
            $sgID = @min(
                self::getSubObjectIDs('StorageGroup')
            );
        }
        /**
         * Load the storage group.
         */
        $StorageGroup = new StorageGroup($sgID);
        $StorageGroups = self::getClass('StorageGroupManager')
            ->buildSelectBox($sgID);
        /**
         * Get the master node for path display.
         */
        $StorageNode = $StorageGroup->getMasterStorageNode();
        $gtID = 1;
        if ($gametype > 0) {
            $gtID = $gametype;
        }
        $GameTypes = self::getClass('GameTypeManager')
            ->buildSelectBox($gtID);
        if (!isset($syncmethod)) {
            $syncmethod = 'rsync';
        }
        $syncMethodOptions = sprintf(
            '<select name="syncmethod" id="syncmethod" class="form-control">'
            . '<option value="rsync"%s>%s</option>'
            . '<option value="robocopy"%s>%s</option>'
            . '<option value="smb"%s>%s</option>'
            . '<option value="nfs"%s>%s</option>'
            . '</select>',
            (
                $syncmethod == 'rsync' ?
                ' selected' :
                ''
            ),
            _('Rsync'),
            (
                $syncmethod == 'robocopy' ?
                ' selected' :
                ''
            ),
            _('Robocopy'),
            (
                $syncmethod == 'smb' ?
                ' selected' :
                ''
            ),
            _('SMB'),
            (
                $syncmethod == 'nfs' ?
                ' selected' :
                ''
            ),
            _('NFS')
        );
        $fields = array(
            '<label for="gName">'
            . _('Game Name')
            . '</label>' => '<div class="input-group">'
            . '<input class="form-control gamename-input" type="text" '
            . 'name="name" id="gName" '
            . 'value="'
            . $name
            . '"/>'
            . '</div>',
            '<label for="description">'
            . _('Game Description')
            . '</label>' => '<div class="input-group">'
            . '<textarea name="description" class="form-control gamedesc-input" '
            . 'id="description">'
            . $desc
            . '</textarea>',
            '<label for="version">'
            . _('Game Version')
            . '</label>' => '<div class="input-group">'
            . '<input class="form-control gameversion-input" type="text" '
            . 'name="version" id="version" '
            . 'value="'
            . $version
            . '"/>'
            . '</div>',
            '<label for="storagegroup">'
            . _('Storage Group')
            . '</label>' => $StorageGroups,
            '<label for="gFile">'
            . _('Game Path')
            . '</label>' => '<div class="input-group">'
            . '<span class="input-group-addon">'
            . $StorageNode->get('path')
            . '/'
            . '</span>'
            . '<input type="text" class="form-control gamefile-input" '
            . 'name="file" id="gFile" '
            . 'value="'
            . $file
            . '"/>',
            '<label for="gametype">'
            . _('Game Type')
            . '</label>' => $GameTypes,
            '<label for="syncmethod">'
            . _('Sync Method')
            . '</label>' => $syncMethodOptions,
            '<label for="isEnabled">'
            . _('Game Enabled')
            . '</label>' => '<input type="checkbox" '
            . 'name="isEnabled" id="isEnabled" checked/>',
            '<label for="toRep">'
            . _('Replicate?')
            . '</label>' => '<input type="checkbox" '
            . 'name="toReplicate" id="toRep" checked/>',
            '<label for="add">'
            . _('Create Game')
            . '</label>' => '<button class="btn btn-info btn-block" type="submit" '
            . 'id="add" name="add">'
            . _('Add')
            . '</button>'
        );
        self::$HookManager
            ->processEvent(
                'GAME_ADD',
                array(
                    'headerData' => &$this->headerData,
                    'data' => &$this->data,
                    'templates' => &$this->templates,
                    'attributes' => &$this->attributes
                )
            );
        array_walk($fields, $this->fieldsToData);
        unset($fields);
        self::$HookManager
            ->processEvent(
                'GAME_ADD_POST',
                array(
                    'headerData' => &$this->headerData,
                    'data' => &$this->data,
                    'templates' => &$this->templates,
                    'attributes' => &$this->attributes
                )
            );
        echo '<div class="col-xs-9">';
        echo '<div class="panel panel-info">';
        echo '<div class="panel-heading text-center">';
        echo '<h4 class="title">';
        echo $this->title;
        echo '</h4>';
        echo '</div>';
        echo '<div class="panel-body">';
        echo '<form class="form-horizontal" method="post" action="'
            . $this->formAction
            . '">';
        $this->render(12);
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    /**
     * Edit game page.
     *
     * @return void
     */
    public function edit()
    {
        echo '<div class="col-xs-9 tab-content">';
        $this->gameGeneral();
        echo '</div>';
    }
    /**
     * Actually submit the creation of the game.
     *
     * @return void
     */
    public function addPost()
    {
        self::$HookManager->processEvent('GAME_ADD_POST');
        $file = trim(
            filter_input(INPUT_POST, 'file')
        );
        $name = trim(
            filter_input(INPUT_POST, 'name')
        );
        $desc = trim(
            filter_input(INPUT_POST, 'description')
        );
        $version = trim(
            filter_input(INPUT_POST, 'version')
        );
        $storagegroup = (int)filter_input(INPUT_POST, 'storagegroup');
        $gametype = (int)filter_input(INPUT_POST, 'gametype');
        $syncmethod = filter_input(INPUT_POST, 'syncmethod');
        $isenabled = (int)isset($_POST['isEnabled']);
        $torep = (int)isset($_POST['toReplicate']);
        try {
            if (self::getClass('GameManager')->exists($name)) {
                throw new Exception(_('A game already exists with this name!'));
            }
            if (self::getClass('GameManager')->exists($file, '', 'downloadPath')) {
                throw new Exception(
                    sprintf(
                        '%s, %s.',
                        _('Please choose a different path'),
                        _('this one is already in use by another game')
                    )
                );
            }
            $Game = self::getClass('Game')
                ->set('name', $name)
                ->set('description', $desc)
                ->set('version', $version)
                ->set('downloadPath', $file)
                ->set('state', $isenabled);
            if (!$Game->save()) {
                throw new Exception(_('Add game failed!'));
            }
            $hook = 'GAME_ADD_SUCCESS';
            $msg = json_encode(
                array(
                    'msg' => _('Game added!'),
                    'title' => _('Game Create Success')
                )
            );
        } catch (Exception $e) {
            $hook = 'GAME_ADD_FAIL';
            $msg = json_encode(
                array(
                    'error' => $e->getMessage(),
                    'title' => _('Game Create Fail')
                )
            );
        }
        self::$HookManager
            ->processEvent(
                $hook,
                array('Game' => &$Game)
            );
        unset($Game);
        echo $msg;
        exit;
    }
    /**
     * Display game general information.
     *
     * @return void
     */
    public function gameGeneral()
    {
        unset(
            $this->data,
            $this->form,
            $this->templates,
            $this->attributes,
            $this->headerData
        );
        $this->attributes = array(
            array('class' => 'col-xs-4'),
            array('class' => 'col-xs-8 form-group'),
        );
        $this->templates = array(
            '${field}',
            '${input}',
        );
        $name = (
            filter_input(INPUT_POST, 'name') ?: $this->obj->get('name')
        );
        $desc = (
            filter_input(INPUT_POST, 'description') ?: $this->obj->get('description')
        );
        $version = (
            filter_input(INPUT_POST, 'version') ?: $this->obj->get('version')
        );
        $isen = (int)isset($_POST['isEnabled']);
        if (!$isen) {
            $isen = $this->obj->get('isEnabled');
        }
        if ($isen) {
            $isen = ' checked';
        } else {
            $isen = '';
        }
        $torep = (int)isset($_POST['toReplicate']);
        if (!$torep) {
            $torep = $this->obj->get('toReplicate');
        }
        if ($torep) {
            $torep = ' checked';
        } else {
            $torep = '';
        }
        $toprot = (int)isset($_POST['protected_game']);
        if (!$toprot) {
            $toprot = $this->obj->get('protected');
        }
        if ($toprot) {
            $toprot = ' checked';
        } else {
            $toprot = '';
        }
        $file = trim(
            filter_input(INPUT_POST, 'file')
        );
        if (!$file) {
            $file = $this->obj->get('downloadPath');
        }
        $fields = array(
            '<label for="gName">'
            . _('Game Name')
            . '</label>' => '<div class="input-group">'
            . '<input class="form-control gamename-input" type="text" '
            . 'name="name" id="gName" '
            . 'value="'
            . $name
            . '"/>'
            . '</div>',
            '<label for="description">'
            . _('Game Description')
            . '</label>' => '<div class="input-group">'
            . '<textarea name="description" class="form-control gamedesc-input" '
            . 'id="description">'
            . $desc
            . '</textarea>',
            '<label for="version">'
            . _('Game Version')
            . '</label>' => '<div class="input-group">'
            . '<input class="form-control gameversion-input" type="text" '
            . 'name="version" id="version" '
            . 'value="'
            . $version
            . '"/>'
            . '</div>',
            '<label for="gFile">'
            . _('Game Path')
            . '</label>' => '<div class="input-group">'
            . '<input type="text" class="form-control gamefile-input" '
            . 'name="file" id="gFile" '
            . 'value="'
            . $file
            . '"/>',
            '<label for="isEnabled">'
            . _('Game Enabled')
            . '</label>' => '<input type="checkbox" '
            . 'name="isEnabled" id="isEnabled"'
            . $isen
            . '/>',
            '<label for="updategen">'
            . _('Make Changes?')
            . '</label>' => '<button class="btn btn-info btn-block" type="submit" '
            . 'id="updategen" name="update">'
            . _('Update')
            . '</button>'
        );
        self::$HookManager
            ->processEvent(
                'GAME_FIELDS',
                array(
                    'fields' => &$fields,
                    'Game' => &$this->obj
                )
            );
        array_walk($fields, $this->fieldsToData);
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
        echo '<!-- General -->';
        echo '<div class="tab-pane fade in active" id="game-gen">';
        echo '<div class="panel panel-info">';
        echo '<div class="panel-heading text-center">';
        echo '<h4 class="title">';
        echo _('Game General');
        echo '</h4>';
        echo '</div>';
        echo '<div class="panel-body">';
        echo '<form class="form-horizontal" method="post" action="'
            . $this->formAction
            . '&tab=game-gen">';
        $this->render(12);
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        unset(
            $this->data,
            $this->form,
            $this->templates,
            $this->attributes,
            $this->headerData
        );
    }
    /**
     * Display game storage groups.
     *
     * @return void
     */
    public function gameStoragegroups()
    {
        unset(
            $this->data,
            $this->form,
            $this->templates,
            $this->attributes,
            $this->headerData
        );
        $this->headerData = array(
            '<label for="toggler2">'
            . '<input type="checkbox" name="toggle-checkboxgroup1" '
            . 'class="toggle-checkbox1" id="toggler2"/>'
            . '</label>',
            _('Storage Group Name')
        );
        $this->templates = array(
            '<label for="sg-${storageGroup_id}">'
            . '<input type="checkbox" name="storagegroup[]" class='
            . '"toggle-group" id="sg-${storageGroup_id}" '
            . 'value="${storageGroup_id}"/>'
            . '</label>',
            '<a href="?node=storage&editStorageGroup&id=${storageGroup_id}">'
            . '${storageGroup_name}'
            . '</a>'
        );
        $this->attributes = array(
            array(
                'class' => 'parser-false filter-false',
                'width' => 16
            ),
            array(),
        );
        Route::listem('storagegroup');
        $StorageGroups = json_decode(
            Route::getData()
        );
        $StorageGroups = $StorageGroups->storagegroups;
        foreach ((array)$StorageGroups as &$StorageGroup) {
            $groupinme = in_array(
                $StorageGroup->id,
                $this->obj->get('storagegroups')
            );
            if ($groupinme) {
                continue;
            }
            $this->data[] = array(
                'storageGroup_id' => $StorageGroup->id,
                'storageGroup_name' => $StorageGroup->name,
            );
            unset($StorageGroup);
        }
        self::$HookManager->processEvent(
            'GAME_ADD_STORAGE_GROUP',
            array(
                'data' => &$this->data,
                'headerData' => &$this->headerData,
                'templates' => &$this->templates,
                'attributes' => &$this->attributes
            )
        );
        echo '<!-- Storage Groups -->';
        echo '<div class="tab-pane fade" id="game-storage">';
        echo '<div class="panel panel-info">';
        echo '<div class="panel-heading text-center">';
        echo '<h4 class="title">';
        echo _('Game Storage Groups');
        echo '</h4>';
        echo '</div>';
        echo '<div class="panel-body">';
        echo '<form class="form-horizontal" method="post" action="'
            . $this->formAction
            . '&tab=game-storage">';
        if (is_array($this->data) && count($this->data)) {
            echo '<div class="text-center">';
            echo '<div class="checkbox">';
            echo '<label for="groupMeShow">';
            echo '<input type="checkbox" name="groupMeShow" '
                . 'id="groupMeShow"/>';
            echo _('Check here to see what storage groups can be added');
            echo '</label>';
            echo '</div>';
            echo '</div>';
            echo '<br/>';
            echo '<div class="hiddeninitially groupNotInMe panel panel-info" '
                . 'id="groupNotInMe">';
            echo '<div class="panel-heading text-center">';
            echo '<h4 class="title">';
            echo _('Add Storage Groups');
            echo '</h4>';
            echo '</div>';
            echo '<div class="panel-body">';
            $this->render(12);
            echo '<div class="form-group">';
            echo '<label for="updategroups" class="control-label col-xs-4">';
            echo _('Add selected storage groups');
            echo '</label>';
            echo '<div class="col-xs-8">';
            echo '<button type="submit" name="updategroups" class='
                . '"btn btn-info btn-block" id="updategroups">'
                . _('Add')
                . '</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        unset(
            $this->data,
            $this->headerData,
            $this->templates,
            $this->attributes
        );
        $this->headerData = array(
            '<label for="toggler3">'
            . '<input type="checkbox" name="toggle-checkbox" '
            . 'class="toggle-checkboxAction" id="toggler3"/>'
            . '</label>',
            '',
            _('Storage Group Name')
        );
        $this->templates = array(
            '<label for="sg1-${storageGroup_id}">'
            . '<input type="checkbox" name="storagegroup-rm[]" class='
            . '"toggle-group" id="sg1-${storageGroup_id}" '
            . 'value="${storageGroup_id}"/>'
            . '</label>',
            '<div class="radio">'
            . '<input type="radio" class="default" '
            . 'name="primary" id="group${storageGroup_id}" '
            . 'value="${storageGroup_id}" ${is_primary}/>'
            . '<label for="group${storageGroup_id}">'
            . '</label>'
            . '</div>',
            '<a href="?node=storage&editStorageGroup&id=${storageGroup_id}">'
            . '${storageGroup_name}'
            . '</a>'
        );
        $this->attributes = array(
            array(
                'class' => 'parser-false filter-false',
                'width' => 16
            ),
            array(
                'class' => 'filter-false',
                'width' => 16
            ),
            array(),
        );
        foreach ((array)$this->obj->get('storagegroups') as &$groupid) {
            if (!$groupid > 0) {
                continue;
            }
            $StorageGroup = new StorageGroup($groupid);
            if (!$StorageGroup->isValid()) {
                continue;
            }
            $primary = '';
            if ($this->obj->getPrimaryGroup($groupid)) {
                $primary = 'checked';
            }
            $this->data[] = array(
                'storageGroup_id' => $groupid,
                'storageGroup_name' => $StorageGroup->get('name'),
                'is_primary' => $primary
            );
            unset($StorageGroup, $groupid);
        }
        self::$HookManager->processEvent(
            'GAME_STORAGE_GROUPS',
            array(
                'data' => &$this->data,
                'headerData' => &$this->headerData,
                'templates' => &$this->templates,
                'attributes' => &$this->attributes
            )
        );
        if (is_array($this->data) && count($this->data)) {
            echo '<div class="panel panel-info">';
            echo '<div class="panel-heading text-center">';
            echo '<h4 class="title">';
            echo _('Game Storage Groups');
            echo '</h4>';
            echo '</div>';
            echo '<div class="panel-body">';
            $this->render(12);
            echo '<div class="form-group">';
            echo '<label for="groupdel" class="control-label col-xs-4">';
            echo _('Remove selected storage groups');
            echo '</label>';
            echo '<div class="col-xs-8">';
            echo '<button type="submit" name="groupdel" class='
                . '"btn btn-danger btn-block" id="groupdel">'
                . _('Remove')
                . '</button>';
            echo '</div>';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label for="primarysel" class="control-label col-xs-4">';
            echo _('Set primary storage group');
            echo '</label>';
            echo '<div class="col-xs-8">';
            echo '<button type="submit" name="primarysel" class='
                . '"btn btn-info btn-block" id="primarysel">'
                . _('Update')
                . '</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    /**
     * Submit save/update the game.
     *
     * @return void
     */
    public function editPost()
    {
        self::$HookManager
            ->processEvent(
                'GAME_EDIT_POST',
                array(
                    'Game' => &$this->obj
                )
            );
        global $tab;
        $name = trim(
            filter_input(INPUT_POST, 'name')
        );
        $file = trim(
            filter_input(INPUT_POST, 'file')
        );
        $desc = trim(
            filter_input(INPUT_POST, 'description')
        );
        $version = trim(
            filter_input(INPUT_POST, 'version')
        );
        $gametype = (int)filter_input(INPUT_POST, 'gametype');
        $syncmethod = filter_input(INPUT_POST, 'syncmethod');
        $protected = (int)isset($_POST['protected_game']);
        $isEnabled = (int)isset($_POST['isEnabled']);
        $toReplicate = (int)isset($_POST['toReplicate']);
        $items = filter_input_array(
            INPUT_POST,
            array(
                'storagegroup' => array(
                    'flags' => FILTER_REQUIRE_ARRAY
                ),
                'storagegroup-rm' => array(
                    'flags' => FILTER_REQUIRE_ARRAY
                )
            )
        );
        $storagegroup = $items['storagegroup'];
        $storagegrouprm = $items['storagegroup-rm'];
        $primary = (int)filter_input(
            INPUT_POST,
            'primary'
        );
        try {
            switch ($tab) {
            case 'game-gen':
                if ($this->obj->get('name') != $name
                    && self::getClass('GameManager')->exists(
                        $name,
                        $this->obj->get('id')
                    )
                ) {
                    throw new Exception(
                        _('A game already exists with this name!')
                    );
                }
                $exists = self::getClass('GameManager')
                    ->exists(
                        $file,
                        '',
                        'downloadPath'
                    );
                if ($this->obj->get('downloadPath') != $file
                    && $exists
                ) {
                    throw new Exception(
                        sprintf(
                            '%s, %s.',
                            _('Please choose a different path'),
                            _('this one is already in use by another game')
                        )
                    );
                }
                $this
                    ->obj
                    ->set('name', $name)
                    ->set('description', $desc)
                    ->set('version', $version)
                    ->set('downloadPath', $file)
                    ->set('state', $isEnabled);
                break;
            case 'game-storage':
                if (isset($_POST['updategroups'])) {
                    $this->obj->addStorageGroup($storagegroup);
                } elseif (isset($_POST['primarysel'])) {
                    $this->obj->setPrimaryGroup($primary);
                } elseif (isset($_POST['groupdel'])) {
                    $groupdel = count($storagegrouprm);
                    $ingroups = count($this->obj->get('storagegroups'));
                    if ($groupdel < 1) {
                        throw new Exception(
                            _('No groups selected to be removed')
                        );
                    }
                    if ($ingroups < 2) {
                        throw new Exception(
                            _('You must have at least one group associated')
                        );
                    }
                    $this
                        ->obj
                        ->removeStorageGroup(
                            $storagegrouprm
                        );
                }
                break;
            }
            if (!$this->obj->save()) {
                throw new Exception(
                    _('Game update failed!')
                );
            }
            $hook = 'GAME_UPDATE_SUCCESS';
            $msg = json_encode(
                array(
                    'msg' => _('Game updated!'),
                    'title' => _('Game Update Success')
                )
            );
        } catch (Exception $e) {
            $hook = 'GAME_UPDATE_FAIL';
            $msg = json_encode(
                array(
                    'error' => $e->getMessage(),
                    'title' => _('Game Update Fail')
                )
            );
        }
        self::$HookManager
            ->processEvent(
                $hook,
                array('Game' => &$this->obj)
            );
        echo $msg;
        exit;
    }
    /**
     * Presents the membership information for games
     * (both hosts and groups)
     *
     * @return void
     */
    public function membership()
    {
        unset(
            $this->data,
            $this->form,
            $this->headerData,
            $this->templates,
            $this->attributes
        );
        echo '<!-- Game Membership -->';
        echo '<div class="col-xs-9">';
        echo '<div class="tab-pane fade in active" id="'
            . $this->node
            . '-membership">';
        echo '<div class="panel panel-info">';
        echo '<div class="panel-heading text-center">';
        echo '<h4 class="title">';
        echo _('Game Membership');
        echo '</h4>';
        echo '</div>';
        echo '<div class="panel-body">';
        echo '<ul class="nav nav-tabs" role="tablist">';
        echo '<li role="presentation" class="active">';
        echo '<a href="#game-hosts" aria-controls="game-hosts" '
            . 'role="tab" data-toggle="tab">';
        echo _('Hosts');
        echo '</a>';
        echo '</li>';
        echo '<li role="presentation">';
        echo '<a href="#game-groups" aria-controls="game-groups" '
            . 'role="tab" data-toggle="tab">';
        echo _('Groups');
        echo '</a>';
        echo '</li>';
        echo '</ul>';
        echo '<div class="tab-content">';
        /**
         * Hosts Tab
         */
        $this->gameMembershipHosts();
        /**
         * Groups Tab
         */
        $this->gameMembershipGroups();
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    /**
     * Display host membership for games
     *
     * @return void
     */
    protected function gameMembershipHosts()
    {
        unset(
            $this->data,
            $this->form,
            $this->headerData,
            $this->templates,
            $this->attributes
        );
        echo '<div role="tabpanel" class="tab-pane active" id="game-hosts">';
        echo '<form class="form-horizontal" method="post" action="'
            . $this->formAction
            . '">';
        $this->headerData = array(
            '<label for="togglerhost1">'
            . '<input type="checkbox" name="toggle-checkboxhost1" '
            . 'class="toggle-checkbox1" id="togglerhost1"/>'
            . '</label>',
            _('Host Name')
        );
        $this->templates = array(
            '<label for="host-${host_id}">'
            . '<input type="checkbox" name="host[]" class="toggle-host1" '
            . 'id="host-${host_id}" value="${host_id}"/>'
            . '</label>',
            '<a href="?node=host&sub=edit&id=${host_id}">${host_name}</a>'
        );
        $this->attributes = array(
            array(
                'width' => 16,
                'class' => 'parser-false filter-false'
            ),
            array()
        );
        Route::names(
            'host',
            ['id' => $this->obj->get('hostsnotinme')]
        );
        $hostsnotinme = json_decode(
            Route::getData()
        );
        foreach ((array)$hostsnotinme as &$item) {
            $this->data[] = [
                'host_id' => $item->id,
                'host_name' => $item->name
            ];
            unset($item);
        }
        if (isset($this->data) && count($this->data ?: []) > 0) {
            echo '<div class="text-center">';
            echo '<div class="checkbox">';
            echo '<label for="hostMeShow">';
            echo '<input type="checkbox" name="hostMeShow" id="hostMeShow"/>';
            echo _('Check here to see what hosts can be added');
            echo '</label>';
            echo '</div>';
            echo '</div>';
            echo '<br/>';
            echo '<div class="hiddeninitially panel panel-info" id="hostNotInMe">';
            echo '<div class="panel-heading text-center">';
            echo '<h4 class="title">';
            echo _('Add Hosts');
            echo '</h4>';
            echo '</div>';
            echo '<div class="panel-body">';
            $this->render(12);
            echo '<div class="form-group">';
            echo '<label for="updatehosts" class="control-label col-xs-4">';
            echo _('Add selected hosts');
            echo '</label>';
            echo '<div class="col-xs-8">';
            echo '<button type="submit" name="addHosts" '
                . 'id="updatehosts" class="btn btn-info btn-block">'
                . _('Add')
                . '</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        unset(
            $this->data,
            $this->form,
            $this->headerData,
            $this->templates
        );
        $this->headerData = array(
            '<label for="togglerhost2">'
            . '<input type="checkbox" name="toggle-checkbox" '
            . 'class="toggle-checkboxAction" id="togglerhost2"/></label>',
            _('Host Name')
        );
        $this->templates = array(
            '<label for="hostrm-${host_id}">'
            . '<input type="checkbox" name="hostdel[]" '
            . 'value="${host_id}" class="toggle-action" id="'
            . 'hostrm-${host_id}"/>'
            . '</label>',
            '<a href="?node=host&sub=edit&id=${host_id}">${host_name}</a>'
        );
        Route::names(
            'host',
            ['id' => $this->obj->get('hosts')]
        );
        $hostsinme = json_decode(
            Route::getData()
        );
        foreach ((array)$hostsinme as &$item) {
            $this->data[] = [
                'host_id' => $item->id,
                'host_name' => $item->name
            ];
            unset($item);
        }
        if (isset($this->data) && count($this->data ?: []) > 0) {
            echo '<div class="panel panel-warning">';
            echo '<div class="panel-heading text-center">';
            echo '<h4 class="title">';
            echo _('Remove Hosts');
            echo '</h4>';
            echo '</div>';
            echo '<div class="panel-body">';
            $this->render(12);
            echo '<div class="form-group">';
            echo '<label for="remhosts" class="control-label col-xs-4">';
            echo _('Remove selected hosts');
            echo '</label>';
            echo '<div class="col-xs-8">';
            echo '<button type="submit" name="remhosts" class='
                . '"btn btn-danger btn-block" id="remhosts">'
                . _('Remove')
                . '</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</form>';
        echo '</div>';
    }
    /**
     * Display group membership for games
     *
     * @return void
     */
    protected function gameMembershipGroups()
    {
        unset(
            $this->data,
            $this->form,
            $this->headerData,
            $this->templates,
            $this->attributes
        );
        echo '<div role="tabpanel" class="tab-pane" id="game-groups">';
        echo '<form class="form-horizontal" method="post" action="'
            . $this->formAction
            . '">';
        $this->headerData = array(
            '<label for="togglergroup1">'
            . '<input type="checkbox" name="toggle-checkboxgroup1" '
            . 'class="toggle-checkbox1" id="togglergroup1"/>'
            . '</label>',
            _('Group Name')
        );
        $this->templates = array(
            '<label for="group-${group_id}">'
            . '<input type="checkbox" name="group[]" class="toggle-group1" '
            . 'id="group-${group_id}" value="${group_id}"/>'
            . '</label>',
            '<a href="?node=group&sub=edit&id=${group_id}">${group_name}</a>'
        );
        $this->attributes = array(
            array(
                'width' => 16,
                'class' => 'parser-false filter-false'
            ),
            array()
        );
        Route::names(
            'group',
            ['id' => $this->obj->get('groupsnotinme')]
        );
        $groupsnotinme = json_decode(
            Route::getData()
        );
        foreach ((array)$groupsnotinme as &$item) {
            $this->data[] = [
                'group_id' => $item->id,
                'group_name' => $item->name
            ];
            unset($item);
        }
        if (isset($this->data) && count($this->data ?: []) > 0) {
            echo '<div class="text-center">';
            echo '<div class="checkbox">';
            echo '<label for="groupMeShow">';
            echo '<input type="checkbox" name="groupMeShow" id="groupMeShow"/>';
            echo _('Check here to see what groups can be added');
            echo '</label>';
            echo '</div>';
            echo '</div>';
            echo '<br/>';
            echo '<div class="hiddeninitially panel panel-info" id="groupNotInMe">';
            echo '<div class="panel-heading text-center">';
            echo '<h4 class="title">';
            echo _('Add Groups');
            echo '</h4>';
            echo '</div>';
            echo '<div class="panel-body">';
            $this->render(12);
            echo '<div class="form-group">';
            echo '<label for="updategroups" class="control-label col-xs-4">';
            echo _('Add selected groups');
            echo '</label>';
            echo '<div class="col-xs-8">';
            echo '<button type="submit" name="addGroups" '
                . 'id="updategroups" class="btn btn-info btn-block">'
                . _('Add')
                . '</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        unset(
            $this->data,
            $this->form,
            $this->headerData,
            $this->templates
        );
        $this->headerData = array(
            '<label for="togglergroup2">'
            . '<input type="checkbox" name="toggle-checkbox" '
            . 'class="toggle-checkboxAction" id="togglergroup2"/></label>',
            _('Group Name')
        );
        $this->templates = array(
            '<label for="grouprm-${group_id}">'
            . '<input type="checkbox" name="groupdel[]" '
            . 'value="${group_id}" class="toggle-action" id="'
            . 'grouprm-${group_id}"/>'
            . '</label>',
            '<a href="?node=group&sub=edit&id=${group_id}">${group_name}</a>'
        );
        Route::names(
            'group',
            ['id' => $this->obj->get('groups')]
        );
        $groupsinme = json_decode(
            Route::getData()
        );
        foreach ((array)$groupsinme as &$item) {
            $this->data[] = [
                'group_id' => $item->id,
                'group_name' => $item->name
            ];
            unset($item);
        }
        if (isset($this->data) && count($this->data ?: []) > 0) {
            echo '<div class="panel panel-warning">';
            echo '<div class="panel-heading text-center">';
            echo '<h4 class="title">';
            echo _('Remove Groups');
            echo '</h4>';
            echo '</div>';
            echo '<div class="panel-body">';
            $this->render(12);
            echo '<div class="form-group">';
            echo '<label for="remgroups" class="control-label col-xs-4">';
            echo _('Remove selected groups');
            echo '</label>';
            echo '<div class="col-xs-8">';
            echo '<button type="submit" name="remgroups" class='
                . '"btn btn-danger btn-block" id="remgroups">'
                . _('Remove')
                . '</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</form>';
        echo '</div>';
    }
    /**
     * Handle membership form submissions
     *
     * @return void
     */
    public function membershipPost()
    {
        if (self::$ajax) {
            return;
        }
        $reqitems = filter_input_array(
            INPUT_POST,
            array(
                'host' => array(
                    'flags' => FILTER_REQUIRE_ARRAY
                ),
                'hostdel' => array(
                    'flags' => FILTER_REQUIRE_ARRAY
                ),
                'group' => array(
                    'flags' => FILTER_REQUIRE_ARRAY
                ),
                'groupdel' => array(
                    'flags' => FILTER_REQUIRE_ARRAY
                )
            )
        );
        $host = $reqitems['host'];
        $hostdel = $reqitems['hostdel'];
        $group = $reqitems['group'];
        $groupdel = $reqitems['groupdel'];
        try {
            if (isset($_POST['addHosts'])) {
                $this->obj->addHost($host);
                $msg = _('Hosts added to game successfully');
            }
            if (isset($_POST['remhosts'])) {
                $this->obj->removeHost($hostdel);
                $msg = _('Hosts removed from game successfully');
            }
            if (isset($_POST['addGroups'])) {
                $this->obj->addGroup($group);
                $msg = _('Groups added to game successfully');
            }
            if (isset($_POST['remgroups'])) {
                $this->obj->removeGroup($groupdel);
                $msg = _('Groups removed from game successfully');
            }
            if (!$this->obj->save()) {
                throw new Exception(_('Failed to update game membership'));
            }
            $hook = 'GAME_MEMBERSHIP_UPDATE_SUCCESS';
            self::$HookManager
                ->processEvent(
                    $hook,
                    array('Game' => &$this->obj)
                );
            self::setMessage($msg);
            self::redirect($this->formAction);
        } catch (Exception $e) {
            $hook = 'GAME_MEMBERSHIP_UPDATE_FAIL';
            self::$HookManager
                ->processEvent(
                    $hook,
                    array(
                        'Game' => &$this->obj,
                        'error' => $e->getMessage()
                    )
                );
            self::setMessage($e->getMessage());
            self::redirect($this->formAction);
        }
    }
}
