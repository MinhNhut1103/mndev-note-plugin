jQuery(document).ready(function($) {
    // Variables
    let editingNoteId = null;
    const $notesList = $('#mndev-notes-list');
    const $addNoteForm = $('#mndev-add-note-form');
    const $titleInput = $('#note-title');
    const $contentInput = $('#note-content');
    const $addNoteBtn = $('#add-note-btn');
    const $cancelEditBtn = $('#cancel-edit-btn');
    const $refreshBtn = $('#refresh-notes');
    
    // Initialize
    loadNotes();
    
    // Form submission
    $addNoteForm.on('submit', function(e) {
        e.preventDefault();
        
        const title = $titleInput.val().trim();
        const content = $contentInput.val().trim();
        
        if (!title || !content) {
            showNotice('Please fill in both title and content fields.', 'error');
            return;
        }
        
        if (editingNoteId) {
            updateNote(editingNoteId, title, content);
        } else {
            addNote(title, content);
        }
    });
    
    // Cancel edit
    $cancelEditBtn.on('click', function() {
        resetForm();
    });
    
    // Refresh notes
    $refreshBtn.on('click', function() {
        loadNotes();
        showNotice('Notes refreshed!', 'success');
    });
    
    // Edit note
    $notesList.on('click', '.edit-note', function() {
        const noteId = $(this).data('id');
        const $note = $(`.mndev-note[data-id="${noteId}"]`);
        
        const title = $note.find('.mndev-note-title').text();
        const content = $note.find('.mndev-note-content').html();
        
        // Convert HTML content to text for editing
        const tempDiv = $('<div>').html(content);
        const textContent = tempDiv.text();
        
        enterEditMode(noteId, title, textContent);
    });
    
    // Open note popup
    $notesList.on('click', '.mndev-note-title, .mndev-note-content', function() {
        const $note = $(this).closest('.mndev-note');
        const noteId = $note.data('id');
        openNotePopup(noteId);
    });
    
    // Close popup
    $('#mndev-popup-close').on('click', closeNotePopup);
    $('#mndev-popup-overlay').on('click', function(e) {
        if ($(e.target).is('#mndev-popup-overlay')) {
            closeNotePopup();
        }
    });
    
    // Edit from popup
    $('#mndev-popup-edit').on('click', function() {
        const $overlay = $('#mndev-popup-overlay');
        const noteId = $overlay.data('note-id');
        if (!noteId) return;
        
        const $note = $(`.mndev-note[data-id="${noteId}"]`);
        if (!$note.length) return;
        
        const title = $note.find('.mndev-note-title').text();
        const content = $note.find('.mndev-note-content').html();
        const tempDiv = $('<div>').html(content);
        const textContent = tempDiv.text();
        
        closeNotePopup();
        enterEditMode(noteId, title, textContent);
    });
    
    // Delete note
    $notesList.on('click', '.delete-note', function() {
        const noteId = $(this).data('id');
        const $note = $(`.mndev-note[data-id="${noteId}"]`);
        const title = $note.find('.mndev-note-title').text();
        
        if (confirm(mndev_ajax.strings.confirm_delete + '\n\n"' + title + '"')) {
            deleteNote(noteId);
        }
    });
    
    // Functions
    function loadNotes() {
        $notesList.html('<div class="mndev-loading"><span class="spinner is-active"></span>Loading notes...</div>');
        
        $.ajax({
            url: mndev_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mndev_get_notes',
                nonce: mndev_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    renderNotes(response.data);
                } else {
                    showError('Failed to load notes.');
                }
            },
            error: function() {
                showError('An error occurred while loading notes.');
            }
        });
    }
    
    function renderNotes(notes) {
        if (!notes || notes.length === 0) {
            $notesList.html($('#mndev-empty-template').html());
            return;
        }
        
        const notesHtml = notes.map(function(note, index) {
            const template = $('#mndev-note-template').html();
            return template
                .replace(/{{id}}/g, note.id)
                .replace(/{{title}}/g, escapeHtml(note.title))
                .replace(/{{content}}/g, note.content.replace(/\n/g, '<br>'))
                .replace(/{{created_at}}/g, formatDate(note.created_at))
                .replace(/{{updated_at}}/g, formatDate(note.updated_at))
                .replace(/{{index}}/g, index);
        }).join('');
        
        $notesList.html(notesHtml);
        
        // Add entrance animation to notes
        $('.mndev-note').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });
    }
    
    function addNote(title, content) {
        $addNoteBtn.prop('disabled', true).html('<span class="spinner is-active"></span> Adding...');
        
        $.ajax({
            url: mndev_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mndev_add_note',
                title: title,
                content: content,
                nonce: mndev_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice(mndev_ajax.strings.note_added, 'success');
                    resetForm();
                    
                    // Add a nice animation effect before reloading
                    $notesList.fadeOut(300, function() {
                        loadNotes();
                        $(this).fadeIn(300);
                    });
                } else {
                    showError(response.data.message || 'Failed to add note.');
                }
            },
            error: function() {
                showError('An error occurred while adding the note.');
            },
            complete: function() {
                $addNoteBtn.prop('disabled', false).html('<span class="dashicons dashicons-plus-alt"></span> Add Note');
            }
        });
    }
    
    function updateNote(id, title, content) {
        $addNoteBtn.prop('disabled', true).html('<span class="spinner is-active"></span> Updating...');
        
        $.ajax({
            url: mndev_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mndev_update_note',
                id: id,
                title: title,
                content: content,
                nonce: mndev_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice(mndev_ajax.strings.note_updated, 'success');
                    resetForm();
                    
                    // Highlight the updated note
                    const $updatedNote = $(`.mndev-note[data-id="${id}"]`);
                    if ($updatedNote.length) {
                        $updatedNote.addClass('updated-highlight');
                        setTimeout(() => {
                            $updatedNote.removeClass('updated-highlight');
                        }, 2000);
                    } else {
                        // If note not visible, reload all notes
                        $notesList.fadeOut(300, function() {
                            loadNotes();
                            $(this).fadeIn(300);
                        });
                    }
                } else {
                    showError(response.data.message || 'Failed to update note.');
                }
            },
            error: function() {
                showError('An error occurred while updating the note.');
            },
            complete: function() {
                $addNoteBtn.prop('disabled', false).html('<span class="dashicons dashicons-plus-alt"></span> Add Note');
            }
        });
    }
    
    function deleteNote(id) {
        const $note = $(`.mndev-note[data-id="${id}"]`);
        $note.css('opacity', '0.5');
        
        $.ajax({
            url: mndev_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mndev_delete_note',
                id: id,
                nonce: mndev_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice(mndev_ajax.strings.note_deleted, 'success');
                    
                    // Add a nice deletion animation
                    $note.addClass('deleting');
                    setTimeout(() => {
                        $note.slideUp(300, function() {
                            $(this).remove();
                            // Check if no notes left
                            if ($('.mndev-note').length === 0) {
                                $notesList.fadeOut(300, function() {
                                    $notesList.html($('#mndev-empty-template').html());
                                    $(this).fadeIn(300);
                                });
                            }
                        });
                    }, 200);
                } else {
                    showError(response.data.message || 'Failed to delete note.');
                    $note.css('opacity', '1');
                }
            },
            error: function() {
                showError('An error occurred while deleting the note.');
                $note.css('opacity', '1');
            }
        });
    }
    
    function enterEditMode(id, title, content) {
        editingNoteId = id;
        
        $titleInput.val(title);
        $contentInput.val(content);
        
        $addNoteBtn.html('<span class="dashicons dashicons-edit"></span> Update Note');
        $cancelEditBtn.show();
        
        // Highlight the note being edited
        $('.mndev-note').removeClass('editing');
        $(`.mndev-note[data-id="${id}"]`).addClass('editing');
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $addNoteForm.offset().top - 50
        }, 300);
        
        // Focus on title input
        $titleInput.focus();
    }
    
    function resetForm() {
        editingNoteId = null;
        $addNoteForm[0].reset();
        $addNoteBtn.html('<span class="dashicons dashicons-plus-alt"></span> Add Note');
        $cancelEditBtn.hide();
        $('.mndev-note').removeClass('editing');
    }
    
    function showNotice(message, type) {
        const noticeClass = type === 'success' ? 'success' : 'error';
        const $notice = $(`<div class="mndev-notice ${noticeClass}">${message}</div>`);
        
        $('.wrap h1').after($notice);
        
        setTimeout(function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    function showError(message) {
        showNotice(message, 'error');
    }
    
    function openNotePopup(id) {
        const $note = $(`.mndev-note[data-id="${id}"]`);
        if (!$note.length) return;

        const title = $note.find('.mndev-note-title').text();
        const content = $note.find('.mndev-note-content').html();
        const createdAt = $note.find('.mndev-note-date:first').text().replace(/^Created:\s*/i, '');
        const updatedAt = $note.find('.mndev-note-date:last').text().replace(/^Updated:\s*/i, '');

        const $overlay = $('#mndev-popup-overlay');
        $overlay.data('note-id', id);
        $overlay.find('.mndev-popup-title').text(title);
        $overlay.find('.mndev-popup-content').html(content);
        $overlay.find('.popup-created-at').text(createdAt.trim());
        $overlay.find('.popup-updated-at').text(updatedAt.trim());
        $overlay.addClass('active');
        $('body').css('overflow', 'hidden');
    }

    function closeNotePopup() {
        $('#mndev-popup-overlay').removeClass('active');
        $('body').css('overflow', '');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // Format ngày tháng năm giờ theo định dạng Việt Nam
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        
        const formattedDateTime = `${hours}:${minutes} ${day}/${month}/${year}`;
        
        if (diffDays === 0) {
            const diffHours = Math.ceil(diffTime / (1000 * 60 * 60));
            if (diffHours === 0) {
                const diffMinutes = Math.ceil(diffTime / (1000 * 60));
                if (diffMinutes <= 1) {
                    return 'Vừa xong';
                }
                return `${diffMinutes} phút trước`;
            }
            return diffHours === 1 ? '1 giờ trước' : `${diffHours} giờ trước`;
        } else {
            return formattedDateTime;
        }
    }
    
    // Auto-save functionality (optional enhancement)
    let autoSaveTimer;
    $titleInput.on('input', function() {
        if (editingNoteId) {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                // Could implement auto-save here if needed
            }, 2000);
        }
    });
    
    $contentInput.on('input', function() {
        if (editingNoteId) {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                // Could implement auto-save here if needed
            }, 2000);
        }
    });
    
    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if ($titleInput.val().trim() && $contentInput.val().trim()) {
                $addNoteForm.submit();
            }
        }
        
        // Escape to cancel edit or close popup
        if (e.key === 'Escape') {
            if ($('#mndev-popup-overlay').hasClass('active')) {
                closeNotePopup();
            } else if (editingNoteId) {
                resetForm();
            }
        }
    });
});
