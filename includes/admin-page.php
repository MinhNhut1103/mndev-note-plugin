<div class="wrap mndev-notes-wrapper">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-sticky" style="font-size: 30px; height: 30px; width: 30px; margin-right: 10px;"></span>
        <?php _e('Mndev Notes', 'mndev-plugin'); ?>
    </h1>
    
    <div class="mndev-plugin-info" style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-left: 4px solid #0073aa;">
        <p>
            <strong><?php _e('Author:', 'mndev-plugin'); ?></strong> Minh Nhựt |
            <strong><?php _e('Website:', 'mndev-plugin'); ?></strong> <a href="https://dominhnhut.com/" target="_blank" rel="noopener noreferrer">Mndev dominhnhut.com</a>
        </p>
        <p style="margin-top: 10px; font-style: italic; color: #666;">
            <strong><?php _e('Note:', 'mndev-plugin'); ?></strong> <?php _e('Plugin này để note lại các thông tin code tính năng website Mndev, cân nhắc kỹ trước khi xóa', 'mndev-plugin'); ?>
        </p>
    </div>
    
    <div class="mndev-notes-container">
        <div class="mndev-notes-sidebar">
            <div class="mndev-add-note-form">
                <h2><?php _e('Add New Note', 'mndev-plugin'); ?></h2>
                <form id="mndev-add-note-form">
                    <div class="form-group">
                        <label for="note-title"><?php _e('Title', 'mndev-plugin'); ?></label>
                        <input type="text" id="note-title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="note-content"><?php _e('Content', 'mndev-plugin'); ?></label>
                        <textarea id="note-content" name="content" rows="10" required></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="button button-primary" id="add-note-btn">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <?php _e('Add Note', 'mndev-plugin'); ?>
                        </button>
                        <button type="button" class="button" id="cancel-edit-btn" style="display: none;">
                            <?php _e('Cancel', 'mndev-plugin'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="mndev-notes-main">
            <div class="mndev-notes-header">
                <h2><?php _e('Your Notes', 'mndev-plugin'); ?></h2>
                <div class="mndev-notes-actions">
                    <button class="button" id="refresh-notes">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e('Refresh', 'mndev-plugin'); ?>
                    </button>
                </div>
            </div>
            
            <div class="mndev-notes-list" id="mndev-notes-list">
                <div class="mndev-loading">
                    <span class="spinner is-active"></span>
                    <?php _e('Loading notes...', 'mndev-plugin'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Note Template -->
<script type="text/template" id="mndev-note-template">
    <div class="mndev-note" data-id="{{id}}">
        <div class="mndev-note-header">
            <h3 class="mndev-note-title">{{title}}</h3>
            <div class="mndev-note-actions">
                <button class="button button-small edit-note" data-id="{{id}}">
                    <span class="dashicons dashicons-edit"></span>
                    <?php _e('Edit', 'mndev-plugin'); ?>
                </button>
                <button class="button button-small delete-note" data-id="{{id}}">
                    <span class="dashicons dashicons-trash"></span>
                    <?php _e('Delete', 'mndev-plugin'); ?>
                </button>
            </div>
        </div>
        <div class="mndev-note-content">{{content}}</div>
        <div class="mndev-note-meta">
            <span class="mndev-note-date">
                <span class="dashicons dashicons-calendar"></span>
                <?php _e('Created:', 'mndev-plugin'); ?> {{created_at}}
            </span>
            <span class="mndev-note-date">
                <span class="dashicons dashicons-clock"></span>
                <?php _e('Updated:', 'mndev-plugin'); ?> {{updated_at}}
            </span>
        </div>
    </div>
</script>

<!-- Empty State Template -->
<script type="text/template" id="mndev-empty-template">
    <div class="mndev-empty-state">
        <div class="mndev-empty-icon">
            <span class="dashicons dashicons-sticky" style="font-size: 64px; height: 64px; width: 64px;"></span>
        </div>
        <h3><?php _e('No notes yet', 'mndev-plugin'); ?></h3>
        <p><?php _e('Start by adding your first note using the form on the left.', 'mndev-plugin'); ?></p>
    </div>
</script>

<!-- Note Popup Modal -->
<div class="mndev-popup-overlay" id="mndev-popup-overlay">
    <div class="mndev-popup-modal">
        <div class="mndev-popup-header">
            <h2 class="mndev-popup-title"></h2>
            <button class="mndev-popup-close" id="mndev-popup-close">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        <div class="mndev-popup-content"></div>
        <div class="mndev-popup-footer">
            <span class="mndev-popup-date-created">
                <span class="dashicons dashicons-calendar"></span>
                <strong><?php _e('Created:', 'mndev-plugin'); ?></strong> <span class="popup-created-at"></span>
            </span>
            <span class="mndev-popup-date-updated">
                <span class="dashicons dashicons-clock"></span>
                <strong><?php _e('Updated:', 'mndev-plugin'); ?></strong> <span class="popup-updated-at"></span>
            </span>
        </div>
    </div>
</div>
