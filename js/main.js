// Funkcja globalna do toastów
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

// FUNKCJA GLOBALNA - poza $(document).ready()
function loadGames(search = '', tag = '') {
    $('#loading-spinner').removeClass('d-none');
    $.ajax({
        url: 'ajax_games.php',
        method: 'GET',
        data: { search: search, tag: tag },
        success: function(data) {
            $('#games-list').html(data);
            animateCards();
        },
        error: function(xhr) {
            $('#games-list').html(`
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        Błąd ładowania danych: ${xhr.statusText}
                    </div>
                </div>
            `);
        },
        complete: function() {
            $('#loading-spinner').addClass('d-none');
        }
    });
}

// Inicjalizacja ładowania gier i obsługa interakcji
$(document).ready(function() {
    // Debounce do wyszukiwania
    let searchTimeout;

    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        loadGames($('#search').val(), $('#tag').val());
    });
    
    $('#search, #tag').on('input change', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadGames($('#search').val(), $('#tag').val());
        }, 300);
    });

    loadGames();

    // Aktualizacja licznika koszyka i dropdownu
    updateCartCount();
    loadCartDropdown();

    // AJAX dodawanie do koszyka z toastami
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var gameId = $btn.data('game-id');
        if ($btn.hasClass('in-cart')) return;

        $btn.prop('disabled', true);

        $.post('cart_add.php', {game_id: gameId}, function(response) {
            if (response.status === 'success') {
                $btn
                    .removeClass('btn-success')
                    .addClass('btn-secondary in-cart')
                    .html('<i class="fas fa-check me-2"></i>W koszyku')
                    .prop('disabled', true)
                    .data('in-cart', '1');
                
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

    // AJAX usuwanie z koszyka w dropdownie z toastami
    $(document).on('click', '.remove-from-cart', function() {
        var gameId = $(this).data('game-id');
        $.post('cart_remove.php', {game_id: gameId}, function(resp) {
            if (resp.success) {
                updateCartCount();
                loadCartDropdown();
                loadGames($('#search').val(), $('#tag').val());
                showToast('Usunięto z koszyka');
            } else {
                showToast('Błąd podczas usuwania', 'error');
            }
        }, 'json').fail(function() {
            showToast('Błąd połączenia', 'error');
        });
    });
});

// Dynamiczne ładowanie zawartości koszyka
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

// Aktualizacja licznika koszyka
function updateCartCount() {
    $.get('cart_count.php', function(response) {
        $('#cart-badge').text(response.count);
    }, 'json');
}

// Automatyczna aktualizacja co 3 sekundy
setInterval(() => {
    updateCartCount();
    loadCartDropdown();
}, 3000);

// Animacja kafelków
function animateCards() {
    document.querySelectorAll('.game-card').forEach((card, i) => {
        card.style.opacity = 0;
        card.style.transform = 'translateY(50px)';
        setTimeout(() => {
            card.style.opacity = 1;
            card.style.transform = 'translateY(0)';
        }, 100 + i * 80);
    });
}

// Strzałka powrotu do góry
window.addEventListener('scroll', function() {
    const btn = document.getElementById('backToTop');
    if (btn) {
        btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTop');
    if (backToTopBtn) {
        backToTopBtn.onclick = () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
        function checkBackToTop() {
            if (window.scrollY > 200) {
                backToTopBtn.classList.add("show");
            } else {
                backToTopBtn.classList.remove("show");
            }
        }
        window.addEventListener("scroll", checkBackToTop);
        checkBackToTop();
    }
});
