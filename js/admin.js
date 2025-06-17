$(function() {
    // Live search
    let searchTimeout;
    $('#admin-search').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val();
        searchTimeout = setTimeout(() => {
            $.get('admin.php', { 
                search: searchTerm,
                ajax: 1 
            }, function(data) {
                $('#admin-games-list').html(data);
            });
        }, 300);
    });

    // Blokuj domyślne wysłanie formularza
    $('#admin-search-form').on('submit', function(e) {
        e.preventDefault();
    });

    // AJAX edycja ceny gry (delegacja!)
    $(document).on('submit', '.ajax-edit-price', function(e) {
        e.preventDefault();
        const $form = $(this);
        const gameId = $form.data('game-id');
        const price = $form.find('input[name="price"]').val();
        $.post('admin_ajax.php', {
            action: 'edit_price',
            game_id: gameId,
            price: price
        }, function(resp) {
            if (resp.new_price) {
                $form.closest('.game-card').find('.game-price').text(resp.new_price + ' PLN');
            }
        }, 'json');
    });

    // AJAX edycja tagów gry (delegacja!)
    $(document).on('submit', '.ajax-edit-tags', function(e) {
        e.preventDefault();
        const $form = $(this);
        const gameId = $form.data('game-id');
        const tags = $form.find('input[name="tags"]').val();
        $.post('admin_ajax.php', {
            action: 'edit_tags',
            game_id: gameId,
            tags: tags
        }, function(resp) {
            if (resp.new_tags !== undefined) {
                const $tagsContainer = $form.closest('.game-card').find('.tags-container');
                $tagsContainer.empty();
                if (resp.new_tags.trim() === '') {
                    $tagsContainer.append('<span class="tag-badge tag-badge-empty"><i class="fa fa-tag"></i>Brak tagów</span>');
                } else {
                    resp.new_tags.split(',').forEach(function(tag) {
                        tag = tag.trim();
                        if (tag) $tagsContainer.append('<span class="tag-badge"><i class="fa fa-tag"></i>' + $('<div/>').text(tag).html() + '</span>');
                    });
                }
            }
        }, 'json');
    });
});

const lenis = new Lenis({
  lerp: 0.1, // im niższa wartość, tym większe spowolnienie scrolla
  wheelMultiplier: 1
});

function raf(time) {
  lenis.raf(time);
  requestAnimationFrame(raf);
}

requestAnimationFrame(raf);