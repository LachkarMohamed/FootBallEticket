$(document).ready(function () {
    const $slides = $('.featured-slide');
    const $dots = $('.dot');
    const $sidebar = $('.sidebar');
    let currentSlide = 0;
    const slideInterval = 5000;

    function showSlide(index) {
        $slides.each(function (i) {
            $(this).css('opacity', i === index ? '1' : '0');
            $dots.eq(i).toggleClass('active', i === index);
        });
        currentSlide = index;
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % $slides.length;
        showSlide(currentSlide);
    }

    $dots.each(function (i) {
        $(this).on('click', function () {
            showSlide(i);
        });
    });

    showSlide(currentSlide);
    setInterval(nextSlide, slideInterval);

    // Sidebar hover effect
    $sidebar.hover(
        function () {
            $(this).addClass('expanded');
        },
        function () {
            $(this).removeClass('expanded');
        }
    );

    // Fetch and display matches
    $.ajax({
        url: '../PHP/getMatches.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                console.error(data.error);
                return;
            }

            const $matchesContainer = $('#matches-container');
            const reservationFormTemplate = $('#reservation-form-template').html();

            $.each(data, function (index, match) {
                const $matchElement = $('<div>', { class: 'match' });

                $matchElement.html(`
                    <h3>${match.team_home} vs ${match.team_away}</h3>
                    <p>Date: ${new Date(match.date).toLocaleString()}</p>
                    <p>Venue: ${match.venue}</p>
                    ${reservationFormTemplate}
                `);

                $matchElement.find('.reservation-form').on('submit', function (e) {
                    e.preventDefault();
                    const form = this;
                
                    $.ajax({
                        url: '../PHP/isloged.php',
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            if (response.isLogged) {
                                const formData = new FormData(form);
                                formData.set('match_id', match.id);
                
                                $.ajax({
                                    url: '../PHP/reserveTicket.php',
                                    method: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    dataType: 'json',
                                    success: function (data) {
                                        if (data.error) {
                                            alert(data.error);
                                        } else {
                                            alert(data.success);
                                        }
                                    }
                                });
                            } else {
                                window.location.href = '../PHP/login.php';
                            }
                        }
                    });
                });
                

                $matchesContainer.append($matchElement);
            });
        }
    });

    $.ajax({
        url: '../PHP/getReservations.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                console.error(data.error);
                return;
            }

            const $reservationsContainer = $('#reservation-history-container');
            $reservationsContainer.html('<h2>Historique des réservations</h2>');

            $.each(data, function (index, reservation) {
                const $reservationElement = $('<div>', { class: 'reservation' });

                $reservationElement.html(`
                    <p>Match: ${reservation.team_home} vs ${reservation.team_away}</p>
                    <p>Date: ${new Date(reservation.date).toLocaleString()}</p>
                    <p>Venue: ${reservation.venue}</p>
                    <p>Quantity: ${reservation.quantity}</p>
                    <p>Reserved on: ${new Date(reservation.timestamp).toLocaleString()}</p>
                `);

                $reservationsContainer.append($reservationElement);
            });
        }
    });

    $.ajax({
        url: '../PHP/isloged.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.isLogged) {
                $('#login-logout').html('<a href="../PHP/logout.php">LOGOUT</a>');
            } else {
                $('#login-logout').html('<a href="../PHP/login.php">LOGIN</a>');
            }
        }
    });
});
