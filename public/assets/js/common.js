$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    /* Global DataTable Mobile Pagination Fix */
    if (window.innerWidth < 768 && $.fn.DataTable) {
        $.fn.DataTable.ext.pager.numbers_length = 5;
    }

    /*  Fetch Cities */
    $('#state_id').on('change', function () {
        var state_id = $(this).val();
        $('#city_id').empty().append('<option value="">Loading...</option>').prop('disabled', true).trigger('change.select2');

        if (state_id) {
            $.ajax({
                url: APP_URL + '/get-cities/' + state_id,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#city_id').empty().append('<option value="">-- Select City --</option>');
                    $.each(data, function (key, city) {
                        $('#city_id').append(
                            '<option value="' + city.id + '">' + city.city_name + '</option>'
                        );
                    });
                    $('#city_id').prop('disabled', false).trigger('change.select2').trigger('change');
                }
            });
        } else {
            $('#city_id').empty().append('<option value="">Select City</option>').prop('disabled', false).trigger('change.select2').trigger('change');
        }
    });


    /* Fetch Places and Zones */
    $('#city_id').on('change', function () {
        var city_id = $(this).val();
        $('#place_id').empty().append('<option value="">Loading...</option>').prop('disabled', true).trigger('change.select2');
        $('#zone_id').empty().append('<option value="">Loading...</option>').prop('disabled', true).trigger('change.select2');
        if (city_id) {
            $.ajax({
                url: APP_URL + '/get-places/' + city_id,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#place_id').empty().append('<option value="">-- Select Place --</option>');
                    $.each(data, function (key, place) {
                        $('#place_id').append('<option value="' + place.id + '">' + place.place_name + '</option>');
                    });
                    $('#place_id').prop('disabled', false).trigger('change.select2').trigger('change');
                }
            });

            $.ajax({
                url: APP_URL + '/get-zones-by-city/' + city_id,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#zone_id').empty().append('<option value="">-- Select Zone --</option>');
                    $.each(data, function (key, zone) {
                        $('#zone_id').append(
                            '<option value="' + zone.id + '">' + zone.zone_name + '</option>'
                        );
                    });
                    $('#zone_id').prop('disabled', false).trigger('change.select2').trigger('change');
                }
            });
        } else {
            $('#place_id').empty().append('<option value="">Select Place</option>').prop('disabled', false).trigger('change.select2').trigger('change');
            $('#zone_id').empty().append('<option value="">Select Zone</option>').prop('disabled', false).trigger('change.select2').trigger('change');
        }
    });

    /* Store Category -> Raw Materials */
    $(document).on('change', '.store_category', function () {
        let categoryId = $(this).val();
        let materialSelect = $(this).closest('tr').find('.material');
        if (categoryId) {
            $.ajax({
                url: APP_URL + '/raw-materials-by-category/' + categoryId,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    materialSelect.html('<option value="">Select Material</option>');
                    $.each(data, function (key, material) {
                        materialSelect.append('<option value="' + material.id + '" data-uom-id="' + material.uom_id + '">' + material.name + '</option>');
                    });
                }
            });
        } else {
            materialSelect.html('<option value="">Select Material</option>');
        }
    });

    /* Global Double Submission Prevention */
    $(document).on('submit', '.common-form', function (e) {
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');

        if ($form.data('submitted') === true) {
            e.preventDefault();
            return false;
        }

        $form.data('submitted', true);
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...');
    });
});

document.addEventListener('DOMContentLoaded', function () {
    /* ── Build the floating two-panel box ── */
    var mega = document.createElement('div');
    mega.id = 'nd-mega';
    mega.innerHTML = '<div id="nd-left"></div><div id="nd-right"></div>';
    document.body.appendChild(mega);

    var ndLeft = document.getElementById('nd-left');
    var ndRight = document.getElementById('nd-right');
    var openLi = null;

    function shouldUseMega() {
        var layoutMenu = document.getElementById('layout-menu');
        if (!layoutMenu) return false;
        if (!layoutMenu.classList.contains('menu-horizontal')) return false;
        return window.matchMedia && window.matchMedia('(min-width: 1200px)').matches;
    }

    function closeMega() {
        mega.style.display = 'none';
        ndLeft.innerHTML = '';
        ndRight.innerHTML = '';
        ndRight.classList.remove('nd-show');
        if (openLi) { openLi.classList.remove('open'); openLi = null; }
    }

    /* Render right panel for a nested sub */
    function showRight(title, subUl) {
        ndRight.innerHTML = '<div class="nd-title">' + title + '</div>';
        subUl.querySelectorAll(':scope > .menu-item').forEach(function (li) {
            var a = li.querySelector(':scope > .menu-link');
            var label = (li.querySelector(':scope > .menu-link > div') || a).innerText.trim();
            var href = a ? a.getAttribute('href') : '#';
            var isActive = li.classList.contains('active');

            var el = document.createElement('div');
            el.className = 'nd-item' + (isActive ? ' nd-current' : '');
            el.innerText = label;
            el.addEventListener('click', function (e) {
                if (href && href !== '#' && href !== 'javascript:void(0)') {
                    if (e.ctrlKey || e.metaKey) {
                        window.open(href, '_blank');
                    } else {
                        window.location.href = href;
                        closeMega();
                    }
                } else {
                    closeMega();
                }
            });
            ndRight.appendChild(el);
        });
        ndRight.classList.add('nd-show');
    }

    /* Render left panel from a top-level sub UL, positioned below anchor */
    function openMega(subUl, anchorLi) {
        ndLeft.innerHTML = '';
        ndRight.innerHTML = '';
        ndRight.classList.remove('nd-show');

        subUl.querySelectorAll(':scope > .menu-item').forEach(function (li) {
            var a = li.querySelector(':scope > .menu-link');
            var label = (li.querySelector(':scope > .menu-link > div') || a).innerText.trim();
            var nested = li.querySelector(':scope > .menu-sub');
            var isGroup = !!nested;
            var href = (!isGroup && a) ? a.getAttribute('href') : null;
            var isActive = li.classList.contains('active');
            var groupHasActive = isGroup && nested.querySelector('.menu-item.active');

            var el = document.createElement('div');
            el.className = 'nd-item' +
                (isActive ? ' nd-current' : '') +
                (groupHasActive ? ' nd-sel' : '');
            el.innerHTML = '<span>' + label + '</span>' + (isGroup ? '<span class="nd-arr">&#8250;</span>' : '');

            if (isGroup) {
                el.addEventListener('click', function (e) {
                    e.stopPropagation();
                    ndLeft.querySelectorAll('.nd-item').forEach(function (x) { x.classList.remove('nd-sel'); });
                    el.classList.add('nd-sel');
                    showRight(label, nested);
                });
                if (groupHasActive) {
                    setTimeout(function () { el.click(); }, 0);
                }
            } else {
                el.addEventListener('click', function (e) {
                    if (href && href !== '#' && href !== 'javascript:void(0)') {
                        if (e.ctrlKey || e.metaKey) {
                            window.open(href, '_blank');
                        } else {
                            window.location.href = href;
                            closeMega();
                        }
                    } else {
                        closeMega();
                    }
                });
            }
            ndLeft.appendChild(el);
        });

        /* Position below the nav item */
        var r = anchorLi.getBoundingClientRect();
        var top = r.bottom + 4;
        var left = r.left;
        if (left + 490 > window.innerWidth) { left = window.innerWidth - 495; }
        if (left < 4) { left = 4; }

        mega.style.top = top + 'px';
        mega.style.left = left + 'px';
        mega.style.display = 'flex';
    }

    /* ── Bind top-level toggles ── */
    document.querySelectorAll('#layout-menu .menu-inner > .menu-item > .menu-link.menu-toggle')
        .forEach(function (link) {
            link.addEventListener('click', function (e) {
                if (!shouldUseMega()) {
                    closeMega();
                    return;
                }
                e.preventDefault();
                e.stopPropagation();
                var li = this.closest('.menu-item');
                var sub = li.querySelector(':scope > .menu-sub');
                if (!sub) return;
                if (openLi === li) { closeMega(); return; }
                closeMega();
                openLi = li;
                li.classList.add('open');
                openMega(sub, li);
            });
        });

    /* ── Close on outside click / scroll / resize ── */
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#nd-mega') && !e.target.closest('#layout-menu')) {
            closeMega();
        }
    });

    /* Only close on page scroll, NOT on scroll inside the dropdown panels */
    window.addEventListener('scroll', function (e) {
        if (e.target && (e.target === document || e.target === document.documentElement || e.target === document.body)) {
            closeMega();
        }
    }, true);

    window.addEventListener('resize', function () {
        if (!shouldUseMega()) {
            closeMega();
        }
    });
});

function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('profileImagePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
