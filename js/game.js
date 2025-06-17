$(function() {
    // Funkcja do pokazywania eleganckich komunikatów
    function showToast(message, type = 'success') {
        const toast = $(`
            <div class="toast-message toast-${type}">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
            </div>
        `);
        
        let $container = $('#toast-container');
        if (!$container.length) {
            $container = $('<div id="toast-container"></div>').appendTo('body');
        }
        $container.append(toast);
        setTimeout(() => toast.addClass('show'), 100);
        setTimeout(() => {
            toast.removeClass('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Aktualizacja recenzji w czasie rzeczywistym
    function refreshReviews(gameId) {
        $.get('game_reviews_ajax.php', {game_id: gameId}, function(data) {
            $('#reviews-list').html(data.reviews);
            $('#review-count').text(data.count);
            $('.rating-badge').html(`<i class="fa-solid fa-star text-warning"></i> ${data.avg} / 5`);
        }, 'json');
    }

    // Dynamiczne ładowanie zawartości koszyka (identyczne jak main.js)
    function loadCartDropdown() {
        $.get('cart_dropdown.php')
        .done(function(data) {
            $('#cart-dropdown-content').html(data);
        })
        .fail(function(xhr) {
            $('#cart-dropdown-content').html(`
                <div class="text-center text-danger py-2">
                    Błąd ładowania koszyka: ${xhr.statusText}
                </div>
            `);
        });
    }

    // Aktualizacja licznika koszyka (identyczne jak main.js)
    function updateCartCount() {
        $.get('cart_count.php', function(response) {
            $('#cart-badge').text(response.count);
        }, 'json');
    }

    // NOWA FUNKCJA: Aktualizacja stanu przycisku koszyka na stronie gry
    function updateCartButtonState(gameId, inCart) {
        const $btn = $(`.add-to-cart[data-game-id="${gameId}"]`);
        if (inCart) {
            $btn
                .removeClass('btn-green')
                .addClass('btn-secondary in-cart')
                .html('<i class="fas fa-check me-2"></i>W koszyku')
                .prop('disabled', true)
                .data('in-cart', '1');
        } else {
            $btn
                .removeClass('btn-secondary in-cart')
                .addClass('btn-green')
                .html('<i class="fas fa-cart-plus me-2"></i>Dodaj do koszyka')
                .prop('disabled', false)
                .removeData('in-cart');
        }
    }

    // Obsługa recenzji
    $(document).on('submit', '#review-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const gameId = $form.find('[name="game_id"]').val();
        const rating = $('#star-rating-input').val();
        const reviewText = $form.find('[name="review_text"]').val().trim();

        // Walidacja
        if (!rating || rating == 0) {
            showToast('Proszę wybrać ocenę (gwiazdki)', 'error');
            return;
        }

        if (reviewText.length < 10) {
            showToast('Recenzja musi mieć minimum 10 znaków', 'error');
            return;
        }

        $btn.prop('disabled', true);

        $.ajax({
            url: 'review_add.php',
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json'
        })
        .done(function(resp) {
            if (resp.success) {
                showToast('Recenzja dodana pomyślnie!');
                $form.closest('.card').replaceWith(`
                    <div class="alert alert-success border border-success mb-5">
                        <i class="fas fa-check-circle me-2"></i>
                        Dziękujemy za dodanie recenzji!
                    </div>
                `);
                refreshReviews(gameId);
            } else {
                showToast(resp.message || 'Wystąpił błąd', 'error');
            }
        })
        .fail(function() {
            showToast('Błąd połączenia', 'error');
        })
        .always(function() {
            $btn.prop('disabled', false).html('<span class="submit-text">Dodaj recenzję</span>');
        });
    });

    // Obsługa gwiazdek 
    $(document).on('click', '.star-rating .star', function() {
        const val = $(this).data('value');
        $('#star-rating-input').val(val);
        
        $(this).parent().find('.star').each(function(i) {
            if (i < val) {
                $(this).addClass('text-warning');
            } else {
                $(this).removeClass('text-warning');
            }
        });
    });

    // Gwiazdki hover
    $(document).on('mouseenter', '.star-rating .star', function() {
        const val = $(this).data('value');
        $(this).parent().find('.star').each(function(i) {
            if (i < val) {
                $(this).addClass('text-warning');
            } else {
                $(this).removeClass('text-warning');
            }
        });
    }).on('mouseleave', '.star-rating', function() {
        const val = $('#star-rating-input').val();
        $(this).find('.star').each(function(i) {
            if (i < val) {
                $(this).addClass('text-warning');
            } else {
                $(this).removeClass('text-warning');
            }
        });
    });

    // Obsługa ulubionych
    $(document).on('click', '.favorite-toggle', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const gameId = $btn.data('game-id');
        
        $btn.prop('disabled', true);

        $.post('favourite_toggle.php', {game_id: gameId}, function(resp) {
            if (resp.success) {
                const isFavorite = resp.is_favorite;
                if (isFavorite) {
                    $btn
                        .removeClass('btn-outline-neon')
                        .addClass('btn-neon')
                        .html('<i class="fas fa-heart me-2"></i>Usuń z ulubionych');
                } else {
                    $btn
                        .removeClass('btn-neon')
                        .addClass('btn-outline-neon')
                        .html('<i class="far fa-heart me-2"></i>Dodaj do ulubionych');
                }
                showToast(isFavorite ? 'Dodano do ulubionych!' : 'Usunięto z ulubionych');
            } else {
                showToast('Błąd podczas zmiany ulubionych', 'error');
            }
        }, 'json')
        .fail(function() {
            showToast('Błąd połączenia', 'error');
        })
        .always(function() {
            $btn.prop('disabled', false);
        });
    });

    // Koszyk - działa identycznie jak na stronie głównej
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var gameId = $btn.data('game-id');
        if ($btn.hasClass('in-cart')) return;

        $btn.prop('disabled', true);

        $.post('cart_add.php', {game_id: gameId}, function(response) {
            if (response.status === 'success') {
                updateCartButtonState(gameId, true);
                $('#cart-badge').text(response.count || 0);
                updateCartCount();
                loadCartDropdown();
                showToast('Dodano do koszyka!');
            } else {
                showToast('Błąd podczas dodawania do koszyka', 'error');
            }
        }, 'json').fail(function() {
            showToast('Błąd połączenia', 'error');
        }).always(function() {
            if (!$btn.hasClass('in-cart')) {
                $btn.prop('disabled', false).html('<i class="fas fa-cart-plus me-2"></i>Dodaj do koszyka');
            }
        });
    });

    // AJAX usuwanie z koszyka w dropdownie - aktualizuje przycisk na stronie gry
    $(document).on('click', '.remove-from-cart', function() {
        var gameId = $(this).data('game-id');
        var currentGameId = $('.add-to-cart').data('game-id'); // ID gry na bieżącej stronie
        
        $.post('cart_remove.php', {game_id: gameId}, function(resp) {
            if (resp.success) {
                updateCartCount();
                loadCartDropdown();
                showToast('Usunięto z koszyka');
                
                // Jeśli usuwana gra to ta sama co na bieżącej stronie - aktualizuj przycisk
                if (gameId == currentGameId) {
                    updateCartButtonState(gameId, false);
                }
            }
        }, 'json').fail(function() {
            showToast('Błąd połączenia', 'error');
        });
    });

    // Inicjalizacja koszyka przy ładowaniu strony
    updateCartCount();
    loadCartDropdown();
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