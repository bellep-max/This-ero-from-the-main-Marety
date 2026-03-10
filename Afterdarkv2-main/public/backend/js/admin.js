(function($) {
    "use strict"; // Start of use strict
    const addParamElement = $('.param-add');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if($('#update-alert').length) {
        setTimeout(function () {
            $.ajax({
                method: 'POST',
                url: updateCheckerUrl,
                success: function (response) {
                    const element = $('#update-alert');

                    const currentVersion = element.attr('data-version');

                    if (currentVersion !== response.new_version) {
                        element.removeClass('d-none');
                        element.find('.new-version').html(response.new_version);
                    }
                }
            });
        }, 5000)
    }

    // Toggle the side navigation
    $("#sidebarToggle, #sidebarToggleTop").on('click', function() {
        $(window).toggleClass("sidebar-toggled");
        const element = $(".sidebar");

        element.toggleClass("toggled");
        if (element.hasClass("toggled")) {
            document.cookie = 'sidebar=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            document.cookie = "sidebar=small";
            $('.sidebar .collapse').collapse('hide');
        } else {
            document.cookie = 'sidebar=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            document.cookie = "sidebar=large";
        }
    });

    // Close any open menu accordions when window is resized below 768px
    $(window).resize(function() {
        if ($(window).width() < 768) {
            $('.sidebar .collapse').collapse('hide');
        }
    });

    // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
    $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
        if ($(window).width() > 768) {
            const e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        }
    });

    // Scroll to top button appear
    $(document).on('scroll', function() {
        const scrollDistance = $(this).scrollTop();

        if (scrollDistance > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });

    // Smooth scrolling using jQuery easing
    $(document).on('click', 'a.scroll-to-top', function(e) {
        const $anchor = $(this);

        $('html, body').stop().animate({
            scrollTop: ($($anchor.attr('href')).offset().top)
        }, 1000, 'easeInOutExpo');
        e.preventDefault();
    });
    $('#suggest-form').ajaxForm({
        success: function(response, textStatus, xhr, $form) {
            const data = response.data;
            const element = $("#suggest-form");

            const objectType = element.data('objectType');
            const objectId = element.data('objectId');

            let songHtml = '';
            for (let i = 0; i < data.length; i++) {
                songHtml += `<tr>
                    <td class="td-image"><div class="play" data-id="${data[i].id}" data-type="song"><img src="${data[i].artwork_url}"></div></td>
                    <td>${data[i].title}</td>
                    <td>${data[i].artists.map(function(artist) {
                        return artist.name
                    }).join(", ")}</td>
                    <td style="width: 45px; text-align: center;"><span class="add-to" data-id="${objectId}" data-song-id="${data[i].id}" data-type="${objectType}" style="cursor: pointer"><icon class="fa fa-plus"></icon></span></td>
                    </tr>`
            }

            $(".auto-suggest").html(`<table class="table table-striped"><thead><tr><th class="th-image"></th><th>Title</th><th>Artist(s)</th><th></th></tr></thead><tbody>${songHtml}</tbody></table>`);

            $(".add-to").click(function() {
                const songId = $(this).data("song-id");
                const element = $(this).find("icon");

                $.post('/admin/auth/addSong', {
                    object_type: objectType,
                    object_id: objectId,
                    song_id: songId
                }, function(data) {
                    const song = data;
                    element.removeClass("fa-plus");
                    element.addClass("fa-check");
                    element.addClass("check-added");
                    element.addClass("text-success");

                    const html =
                        `<tr id="row-${song.id}">
                            <td class="td-image">
                                <div class="play" data-id="${song.id}" data-type="song"><img src="${song.artwork_url}"></div>
                            </td>
                            <td id="track_${song.id}" class="editable" title="Click to edit">${song.title}</td>
                            <td>${song.artists.map(function(artist) {
                                return artist.name
                            }).join(", ")}</td>
                            <td>${song.album === null ? '' : song.album.title}</td>
                            <td>${song.loves}</td>
                            <td>${song.plays}</td>
                            <td>
                                <a class="row-button edit" href="/admin/songs/edit/${song.id}" data-toggle="tooltip" data-placement="left" title="Edit this song"></a>
                                <a data-id="${objectId}" data-song-id="${song.id}" class="row-button remove" data-type="${objectType}" data-toggle="tooltip" data-placement="left" title="" data-original-title="Remove from ${objectType}"></a>
                            </td>
                        </tr>`;

                    $("#song-row").append(html);
                })
            });
        }
    });

    $(document).on("click", ".remove", function() {
        const objectType = $(this).data("type");
        const objectId = $(this).data("id");
        const songId = $(this).data("song-id");

        $.post('/admin/auth/removeSong', {
            object_type: objectType,
            object_id: objectId,
            song_id: songId
        }, function(data) {
            if (data.success == true) {
                $(`#row-${songId}`).fadeOut();
            }
        });
    });

    const suggestElement = $('.suggest-tracks-form');

    suggestElement.focus(function() {
        return false;
    });

    suggestElement.keyup(function() {
        if ($(this).val()) {
            $('#suggest-form').submit();
            $(".auto-suggest").show();
        } else {
            $(".auto-suggest").hide();
        }
    })

    $(document).keyup(function(e) {
        if (e.keyCode == 27) {
            $(".auto-suggest").hide();
        }
    });

    $(document).on("click", "table tbody tr td.editable", function(e) {
        e.stopPropagation();
        const currentEle = $(this);
        const editClass = $(this).attr("id");
        const value = $.trim($(this).html());
        updateVal(currentEle, value, editClass);
    });

    function updateVal(currentEle, value, editClass) {
        const element = $(`.thVal_${editClass}`);

        $(currentEle).html(`<input class="form-control thVal_${editClass}" type="text" value="${value}"/>`);

        element.focus();
        element.click(function(e) {
            e.stopPropagation();
            return false;
        });
        element.dblclick(function(e) {
            e.stopPropagation();
            return false;
        });

        element.keyup(function(event) {
            if (event.keyCode == 13) {
                const songId = editClass;
                const songName = element.val();
                $.trim($(currentEle).html(element.val()));

                if (songName != value) {
                    $.post('/admin/songs/edit-title', {
                        action: "edit",
                        id: songId.replace('track_', ''),
                        title: songName
                    }, function(data) {

                    });
                }
            }
        });

        element.blur(function (e) {
            const songId = editClass;
            const songName = element.val();
            $.trim($(currentEle).html(element.val()));

            if (songName != value) {
                $.post('/admin/songs/edit-title', {
                    action: "edit",
                    id: songId.replace('track_', ''),
                    title: songName
                }, function(data) {

                });
            }
            $.trim($(currentEle).html(element.val()));
            e.stopPropagation();
        });
    }

    $(document).on("click", "table tbody tr td.lang-editable", function(e) {
        e.stopPropagation();
        const element = $(this);
        const value = $.trim(element.html());
        const input = $("<input/>", {
            class: "form-control",
            type: "text"
        });

        element.html(input);
        input.focus().val(value);
        input.click(function(e) {
            e.stopPropagation();
            return false;
        });
        input.dblclick(function(e) {
            e.stopPropagation();
            return false;
        });
        input.keyup(function(event) {
            if (event.keyCode === 13) {
                const locale = element.data('locale'),
                    group = element.data('group'),
                    key = element.data('key'),
                    uri = element.data('uri');

                if (input.val() !== value) {
                    $.post(uri, {
                        locale: locale,
                        group: group,
                        key: key,
                        uri: uri,
                        value: $.trim(input.val())
                    }, function(data) {

                    });
                }

                element.html($.trim(input.val()));
                e.stopPropagation();
            }
        });
        input.blur(function (e) {
            const locale = element.data('locale'),
                group = element.data('group'),
                key = element.data('key'),
                uri = element.data('uri');

            if (input.val() !== value) {
                $.post(uri, {
                    locale: locale,
                    group: group,
                    key: key,
                    uri: uri
                }, function(data) {

                });
            }

            element.html($.trim(input.val()));
            e.stopPropagation();
        });
    });

    function formatRepo(repo) {
        if (repo.loading) return repo.text;
        let text;

        text = repo.name ? repo.name : repo.title;

        return `<div class="select2-result-repository clearfix">
                    <div class="select2-result-repository__avatar"><img src="${repo.artwork_url}"/></div>
                    <div class="select2-result-repository__meta">
                        <div class="select2-result-repository__title">${text}</div>
                    </div>
                </div>`;
    }

    function formatRepoSelection(repo) {
        const artwork_url = repo.element && repo.element.dataset && repo.element.dataset.artwork
            ? repo.element.dataset.artwork
            : repo.artwork_url;

        let text;

        text = repo.name ? repo.name : repo.title;

        if (repo.element && repo.element.label) {
            text = repo.element.label;
        }

        const markup =
            `<div class="select2-result-repository clearfix">
                <div class="select2-result-repository__avatar"><img src="${artwork_url}"/></div>
                <div class="select2-result-repository__meta">
                    <div class="select2-result-repository__title white">${text}</div>
                </div>
            </div>`;

        return markup || repo.text;
    }

    $(".select-ajax").select2({
        width: '100%',
        theme: "artwork",
        ajax: {
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function(response, params) {
                params.page = params.page || 1;
                return {
                    results: response.data,
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
    }).addClass("select2-with-artwork");

    $(".multi-selector-without-sortable").select2({
        width: '100%',
        placeholder: 'Select one or multi',
        containerCssClass: "with-ajax",
        ajax: {
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                };
            },
            processResults: function(response, params) {
                params.page = params.page || 1;
                return {
                    results: response.data,
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
    });

    $(".multi-selector").select2({
        width: '100%',
        placeholder: 'Select one or multi',
        containerCssClass: "with-ajax",
        ajax: {
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                };
            },
            processResults: function(response, params) {
                params.page = params.page || 1;
                return {
                    results: response.data,
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
    }).on("select2:select", function(evt) {
        const id = evt.params.data.id;
        const element = $(this).children(`option[value="${id}"]`);

        moveElementToEndOfParent(element);

        $(this).trigger("change");
    });

    const element = $(".multi-selector").parent().find("ul.select2-selection__rendered");

    element.sortable({
        containment: 'parent',
        update: function() {
            orderSortedValues();
        }
    });

    function orderSortedValues() {
        $(".multi-selector").parent().find("ul.select2-selection__rendered").children("li[title]").each(function(i, obj) {
            const element = $(".multi-selector").children('option').filter(function() {
                return $(this).html() == obj.title;
            });

            moveElementToEndOfParent(element);
        });
    }

    function moveElementToEndOfParent(element) {
        const parent = element.parent();

        element.detach();

        parent.append(element);
    }

    $(".slide-show-type").select2({
        width: '100%',
        placeholder: 'Select a object type'
    });

    $('.slide-show-type').on("select2:select", function() {
        const type = $(this).val();
        const element = $(".slide-show-selector");

        element.addClass('d-none');
        element.find('select').empty();
        $(`.slide-show-selector[data-type="${type}"]`).removeClass('d-none');
    });

    $(".select2-tags").select2({
        width: '100%',
        tags: true,
        placeholder: 'Select or type the tags',
    });

    $('.select_station_type').on("select2:select", function() {
        const element = $(".broadcast-link");

        $(this).val() == "live"
            ? element.show()
            : element.hide();
    });

    $(function() {
        $('.use_album_cover').change(function() {
            const songId = $(this).data('song-id');
            const albumId = $(this).data('album-id');
            const subaction = $(this).is(':checked') == true
                ? "add"
                : "remove";

            $.post('/admin/ajax.php?do=songs', {
                action: "albumcover",
                song_id: songId,
                album_id: albumId,
                subaction: subaction
            }, function(data) {
                //msg_box("success", "Saved!!!")
            });
        })
    });

    $(".html5-file").fileinput({
        maxAjaxThreads: 5,
        processDelay: 100,
        initialPreviewAsData: true,
        theme: 'fas',
        allowedFileExtensions: ["mp3"],
        uploadExtraData: function() {
            return {
                _token: $("input[name='_token']").val(),
            };
        },
    });

    $('.html5-file').on('fileuploaded', function(event, data, previewId, index) {
        if (data.response.success === true) {
            $(".upload-results").removeClass("hide");
            $("#upload-rows").append(data.response.html);
            $(`#tr_track_${data.response.song.id}`).animate({
                backgroundColor: '#ffe58f'
            }, 'slow');

            setTimeout(function() {
                $(`#tr_track_${data.response.song.id}`).css("background", "none");
            }, 1500)
        }

    });

    $(".datepicker").datepicker();

    $(".table-sortable tbody").sortable({
        handle: '.handle',
        helper: function(e, tr) {
            const helper = $("<span />");
            helper.addClass('engine-ui-sortable-helper');
            helper.css({'width': 'auto', 'height': 'auto'});
            helper.html('Moving item');
            return helper;
        },
    });

    $(document).on('click', '.browse', function() {
        $(this).parent().parent().parent().find('.file-selector').trigger('click');
    });

    $(document).on('change', '.file-selector', function() {
        $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });

    $('.money').mask("###0.00", {reverse: true});
    $('.number').mask("#,##0", {reverse: true});

    $('.post-set-featured-image').on('click', function () {

    });

    function select2WithALlFunction(){
        $('.select2-active').select2({
            placeholder: 'Please select',
            allowClear: false,
            closeOnSelect: true,
        }).on('select2:open', function() {
            const a = $(this).next();
            const b = $(this);

            setTimeout(function() {
                const c = $('.select2-container').find('.select2-results__group');
                c.unbind();
                c.bind( "click", function() {
                    selectAlllickHandler(a, b, c)
                });
            }, 100);
        });
    }

    select2WithALlFunction();

    const selectAlllickHandler = function(a, b, c) {
        c.unbind();
        b.select2('destroy').find('option').prop('selected', 'selected').end();
        select2WithALlFunction();
    };

    $("#check-all").click(function () {
        $("#mass-action-form [type='checkbox']").prop('checked', $(this).prop('checked'));
    });

    $('#start-mass-action').on('click', function(event) {
        event.preventDefault();

        if ($("[name='ids\\[\\]']:checked").length === 0) {
            bootbox.alert({
                title: "Alert",
                message: "Please select an item.",
                centerVertical: true,
                callback: function (result) {
                }
            });

            return false;
        }

        const form = $('#mass-action-form');
        const action = form.find('[name="action"]').val();

        if (!action) {
            bootbox.alert({
                title: "Alert",
                message: "Please select action.",
                centerVertical: true,
                callback: function (result) {
                }
            });

            return false;
        }

        if (action === 'approve') {
            bootbox.confirm({
                title: "Select the songs publishing?",
                message: `Are you sure you want to publish selected (${$("[name='ids\\[\\]']:checked").length}) item?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'not_approve') {
            bootbox.confirm({
                title: "Select the songs publishing?",
                message: `Are you sure you want to send for the moderation selected (${$("[name='ids\\[\\]']:checked").length}) item?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'comment') {
            bootbox.confirm({
                title: "Configure the comments",
                message: `Are you sure you want to enable comments for the selected (${$("[name='ids\\[\\]']:checked").length}) item?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'not_comments') {
            bootbox.confirm({
                title: "Configure the comments",
                message: `Are you sure you want to disable comments for the selected (${$("[name='ids\\[\\]']:checked").length}) item?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'clear_count' || action === 'clear_view') {
            bootbox.confirm({
                title: "Clearing the views counter",
                message: `Are you sure you want to clear the counter of the selected (${$("[name='ids\\[\\]']:checked").length}) item?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'delete') {
            bootbox.confirm({
                title: "Remove",
                message: `Are you sure you want to remove the selected (${$("[name='ids\\[\\]']:checked").length}) item?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'remove_from_album') {
            bootbox.confirm({
                title: "Remove from album",
                message: `Are you sure you want to remove the selected (${$("[name='ids\\[\\]']:checked").length}) from the album?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        }  else if (action === 'remove_from_playlist') {
            bootbox.confirm({
                title: "Remove from playlist",
                message: `Are you sure you want to remove the selected (${$("[name='ids\\[\\]']:checked").length}) from the playlist?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'delete_comment') {
            bootbox.confirm({
                title: "Remove",
                message: `Are you sure you want to remove all the comments by selected (${$("[name='ids\\[\\]']:checked").length}) users?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'fixed') {
            bootbox.confirm({
                title: "Remove",
                message: `Are you sure you want to fixed the selected (${$("[name='ids\\[\\]']:checked").length}) articles?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'not_fixed') {
            bootbox.confirm({
                title: "Remove",
                message: `Are you sure you want to un fixed the selected (${$("[name='ids\\[\\]']:checked").length}) articles?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'set_current') {
            bootbox.confirm({
                title: "Remove",
                message: `Are you sure you want to set current time for the selected (${$("[name='ids\\[\\]']:checked").length}) articles?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else if (action === 'change_author') {
            bootbox.confirm({
                title: "Remove",
                message: `Are you sure you want to set new author for the selected (${$("[name='ids\\[\\]']:checked").length}) item?`,
                centerVertical: true,
                callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }
            });
        } else {
            form.submit();
        }
    });

    $('[data-toggle="tooltip"]').tooltip();

    $(".search-form").submit(function(){
        $("input,select").each(function(index, obj){
            if ($(obj).val() === "") {
                $(obj).attr('disabled', 'disabled');
            }
        });
    });

    $('.post-fullscreen').on('click', function(){
        $('.article-form').toggleClass('fullscreen');
    });

    $('.filter-country-select').on('change', function () {
        const countryCode = $(this).val();

        if (countryCode) {
            $.post(`${window.location.protocol}//${window.location.hostname}/admin/countries/get-city`, {
                countryCode: countryCode,
            }, function(data) {
                $('.filter-city').removeClass('d-none').html(data);
                $('.filter-city-select').removeClass('d-none').html(data);
                select2WithALlFunction();
            });

            $.post(`${window.location.protocol}//${window.location.hostname}/admin/country-languages/get-language`, {
                countryCode: countryCode,
            }, function(data) {
                $('.filter-language').removeClass('d-none');
                $('.filter-language-select').html(data);
                select2WithALlFunction();
            });
        }
    });

    /* API TESTER */
    // create the editor

    try {
        const contentEditor = new JSONEditor(document.getElementById('jsonEditorContent'), {mode: 'view'});
        const responseHeadersEditor = new JSONEditor(document.getElementById('jsonEditorResponseHeaders'), {mode: 'view'});
        const jsonEditorRequestHeaders = new JSONEditor(document.getElementById('jsonEditorRequestHeaders'), {mode: 'view'});
        let timer;

        $('.filter-routes').on('keyup', function () {
            const _this = this;
            clearTimeout(timer);

            timer = setTimeout(function () {
                const search = $(_this).val();
                const regex = new RegExp(search);

                $('ul.routes li').each(function () {
                    if (!regex.test($(this).data('uri'))) {
                        $(this).addClass('d-none');
                    } else {
                        $(this).removeClass('d-none');
                    }
                });
            }, 300);
        });

        $('.route-item a, .route-item button').click(function () {
            const li = $(this).parent('li');

            $('a.method').html(li.data('method')).removeClass(function (index, className) {
                return (className.match(/bg-[^\s]+/) || []).join(' ');
            }).css('background', li.data('method-color'));
            $('.uri').val(li.data('uri'));
            $('input.method').val(li.data('method'));
            $('.param').remove();
            $('.response-tabs').addClass('hide');
            appendParameters(li.data('parameters'));
            $(window).scrollTop(0);
        });

        function getParamCount() {
            return $('.param').length;
        }

        function appendParameters(params) {
            for (let param in params) {
                let html = $('template.param-tpl').html();
                html = html.replace(new RegExp('__index__', 'g'), getParamCount());

                const append = $(html);

                append.find('.param-key').val(params[param].name);
                append.find('.param-val').val(params[param].defaultValue);
                append.find('.param-desc').removeClass('d-none').find('.text').html(params[param].description);

                if (params[param].required == 'true') {
                    append.find('.param-desc .param-required').removeClass('d-none');
                }

                if (params[param].type == 'file') {
                    append.find('.param-val').attr('type', 'file');
                    append.find('.change-val-type i').toggleClass("fa-upload fa-pen");
                }

                addParamElement.before(append);
            }
        }
        $('.params').on('click', '.change-val-type', function () {
            const type = $(this).parent().prev().attr('type') == 'text' ? 'file' : 'text';
            $(this).parent().prev().attr('type', type);
            $("i", this).toggleClass("fa-upload fa-pen");
        }).on('click', '.param-remove', function () {
            $(this).closest('.param').remove();
        });

        addParamElement.on('focus', 'input', function () {
            let html = $('template.param-tpl').html();
            html = html.replace(new RegExp('__index__', 'g'), $('.param').length);
            $(this).closest('.param-add').before(html);
            $('.params .param').last().find('input:first').focus()
        });

        $('.api-tester-form').on('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            if (formData.get('uri').length === 0) {
                return false;
            }

            $.ajax({
                method: 'POST',
                url: api_tester_handle,
                data: formData,
                success: function (data) {
                    if (typeof data === 'object') {
                        if (data.status) {
                            $('#response').removeClass('d-none');
                            $('#error').addClass('d-none');
                            if(data.data.original.contentType === 'application/json') {
                                contentEditor.set(JSON.parse(data.data.original.content));
                                responseHeadersEditor.set(data.data.original.response_headers);
                                jsonEditorRequestHeaders.set(Object.assign(data.request_headers, {'Post Data': data['post_data']}));
                            } else {
                                $('#response').addClass('d-none');
                                $('#error').removeClass('d-none');
                            }
                        } else {
                            alert('failed');
                        }
                    }
                },
                error: function(){
                    $('#response').addClass('d-none');
                    $('#error').removeClass('d-none');
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    } catch(e) {

    }
})(jQuery);


/* Artisan */
$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    const storageKey = function () {
        return `la-${$('#connections').val()}history`;
    };

    function History () {
        this.index = this.count() - 1;
    }

    History.prototype.store = function () {
        let history = localStorage.getItem(storageKey());

        return !history
            ? []
            : JSON.parse(history);
    };

    History.prototype.push = function (record) {
        const history = this.store();
        history.push(record);
        localStorage.setItem(storageKey(), JSON.stringify(history));

        this.index = this.count() - 1;
    };

    History.prototype.count = function () {
        return this.store().length;
    };

    History.prototype.up = function () {
        if (this.index > 0) {
            this.index--;
        }

        return this.store()[this.index];
    };

    History.prototype.down = function () {
        if (this.index < this.count() - 1) {
            this.index++;
        }

        return this.store()[this.index];
    };

    History.prototype.clear = function () {
        localStorage.removeItem(storageKey());
    };

    const history = new History;
    let isRunning = false;

    const send = function () {
        if (isRunning) return false;
        isRunning = true;

        const $input = $('#terminal-query');

        $.ajax({
            url:location.pathname,
            method: 'post',
            data: {
                c: $input.val(),
            },
            success: function (response) {
                history.push($input.val());
                $('#terminal-box')
                    .append(`<div class="item"><small class="label label-default"> > artisan ${$input.val()}<\/small><\/div>`)
                    .append(`<div class="item">${response}<\/div>`);
                $(".output-body").animate({ scrollTop: $('.output-body').prop("scrollHeight")}, 1000);

                $input.val('');
                isRunning = false;
            }
        });
    };

    $('#terminal-query').on('keyup', function (e) {
        e.preventDefault();

        if (e.keyCode === 13) {
            send();
        }

        if (e.keyCode === 38) {
            $(this).val(history.up());
        }

        if (e.keyCode === 40) {
            $(this).val(history.down());
        }
    });

    $('#terminal-clear').click(function () {
        $('#terminal-box').text('');
    });

    $('.loaded-command').click(function () {
        $('#terminal-query').val(`${$(this).html()} `).focus();
    });

    $('#terminal-send').click(function () {
        send();
    });

    /* scheduling */
    $('.run-task').click(function () {
        const id = $(this).data('id');

        $.ajax({
            method: 'POST',
            url: scheduling_run_url,
            data: {
                id: id,
            },
            success: function (data) {
                if (typeof data === 'object') {
                    $('.output-box').removeClass('d-none');
                    $('.output-body').html(data.data);
                }
            }
        });
    });

    /*nested category */
    $('.dd').nestable();
    $('.dd').on('change', function () {
        var $this = $(this);
        var serializedData = window.JSON.stringify($($this).nestable('serialize'));
        $('#cartSortList').val(serializedData);
    });

    /* backup */

    $(".backup-run").click(function() {
        const $btn = $(this);

        $btn.button('loading');

        $.ajax({
            url: $btn.attr('href'),
            method: 'POST',
            success: function (data){
                $('.output-box').removeClass('d-none');
                $('.loading-box').addClass('d-none');

                $('.output-body').html(data.message)
                $btn.removeAttr('disabled');
            }
        });
        $('.loading-box').removeClass('d-none');
        $btn.attr('disabled', 'disabled');

        return false;
    });
    $(".backup-delete").click(function() {
        const $btn = $(this);

        $.ajax({
            url: $btn.attr('href'),
            method: 'DELETE',
            success: function (){
                location.reload();
                $btn.button('reset');
            }
        });

        return false;
    });

    /* post and tiny mce */

    $(document).on("click", "#featured-image .set", function() {
        $('#artwork_picker').trigger('click');
    });

    $("#artwork_picker").change(function(){
        if (this.files && this.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                $('#artwork_url').attr('src', e.target.result);
            }

            reader.readAsDataURL(this.files[0]);
            $('#featured-image').find('.set').addClass('d-none')
            $('#artwork_url').removeClass('d-none');
            $('.post-remove-featured-image').removeClass('d-none');
            $('#remove_artwork').val(0);
        }
    });

    $(document).on("click", ".post-remove-featured-image", function() {
        $('#featured-image').find('.set').removeClass('d-none')
        $('#artwork_url').addClass('d-none');
        $('.post-remove-featured-image').addClass('d-none');
        $('#artwork_picker').val('');
        $('#remove_artwork').val(1);
    });

    $(document).on("click", ".btn-upload-select", function() {
        $('.file-upload').trigger('click');
    });

    $(document).on("change", ".file-upload-form", function() {
        $('.file-upload-form').submit();
    });

    $(document).ready(function () {
        if ($('textarea.post.editor').length) {
            const editor = $('textarea.post.editor').first();
            const filemanagerPluginPath = editor.attr('data-filemanager-plugin-path');
            const responsiveFilemanagerPluginPath= editor.attr('data-responsive-filemanager-plugin-path');
            const externalFilemanagerPath = editor.attr('data-external-filemanager-path');

            tinymce.init({
                selector: 'textarea.post.editor',
                skin: darkMode ? "oxide-dark" : "oxide",
                content_css: darkMode ? "dark" : "default",
                height: 500,
                convert_urls: false,
                relative_urls : false,
                remove_script_host : false,
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template textcolor colorpicker textpattern imagetools codesample toc filemanager responsivefilemanager'
                ],
                toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
                toolbar2: 'responsivefilemanager image media link | forecolor backcolor emoticons | codesample | hiddenBlockInsertButton | spintax',
                image_advtab: true,
                toolbar3: '',
                setup: function (editor) {
                    editor.ui.registry.addButton('hiddenBlockInsertButton', {
                        text: 'Hidden Block',
                        onAction: function (_) {
                            if (editor.selection.getContent().length) {
                                editor.selection.setContent(`[hide]${editor.selection.getContent()}[/hide]`);
                            }
                        }
                    });

                    editor.ui.registry.addButton('spintax', {
                        text: 'Spintax',
                        onAction: function (_) {
                            editor.focus();
                            if (editor.selection.getContent().length) {
                                editor.selection.setContent(`<mark>${editor.selection.getContent()}</mark>`);
                            }
                        }
                    });

                    editor.on('init change', function () {
                        editor.save();
                    });
                },
                external_filemanager_path: externalFilemanagerPath,
                filemanager_title: "File manager",
                external_plugins: {
                    "responsivefilemanager": responsiveFilemanagerPluginPath,
                    "filemanager": filemanagerPluginPath

                },
            });
        }

        if ($('textarea.default.editor').length) {
            tinymce.init({
                selector: 'textarea.default.editor',
                skin: darkMode ? "oxide-dark" : "oxide",
                content_css: darkMode ? "dark" : "default",
                height: 500,
                convert_urls: false,
                relative_urls : false,
                remove_script_host : false,
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template textcolor colorpicker textpattern imagetools codesample toc'
                ],
                toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
                toolbar2: 'link | forecolor backcolor emoticons | codesample',
                image_advtab: true,
                toolbar3: '',
            });
        }
    });

    /* dashboard chart */

    if ($('#revenueSources').length) {
        const ctx = document.getElementById("revenueSources");
        const revenueSources = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: revenueSourcesLabel,
                datasets: [{
                    data: revenueSourcesLabelData,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom',
                },
                cutoutPercentage: 0
            },
        });
    }

    if ($('#subscriptionOverviewChart').length) {
        const ctx = document.getElementById("subscriptionOverviewChart");
        const subscriptionOverviewChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: subscriptionOverviewChartLabel,
                datasets: [{
                    label: "Earnings",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: subscriptionOverviewChartData,
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a dollar sign in the ticks
                            callback: function (value, index, values) {
                                return currencyLabel + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function (tooltipItem, chart) {
                            const datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + currencyLabel + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    }

    $(document).ready(function () {
        const element = $('#fileupload');

        if (element.length) {
            element.each(function () {

                const uri = $(this).attr('action');

                $(this).fileupload({
                    url: uri,
                    maxNumberOfFiles: 1,
                    limitMultiFileUploads: 1000,
                    limitConcurrentUploads: 1
                });
            })

        }
    });

    /* Media Manager */
    $(document).ready(function () {
        const element = $('.media-manager');

        if (element.length) {
            const deleteUri = element.attr('data-delete-uri');
            const moveUri = element.attr('data-move-uri');
            const newFolderUri = element.attr('data-new-folder-uri');
            const indexUri = element.attr('data-index-uri');

            /** diable pjax cache */
            $.pjax.defaults.maxCacheLength = 0;

            /** Create iCheck event */
            function reinventEvent() {
                $('.file-select input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue'
                }).on('ifChanged', function () {
                    if (this.checked) {
                        $(this).closest('tr').css('background-color', '#ffffd5');
                    } else {
                        $(this).closest('tr').css('background-color', '');
                    }
                });

                $('.file-select-all input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue'
                }).on('ifChanged', function () {
                    if (this.checked) {
                        $('.file-select input').iCheck('check');
                    } else {
                        $('.file-select input').iCheck('uncheck');
                    }
                });

                $('table>tbody>tr').mouseover(function () {
                    $(this).find('.btn-group').removeClass('hide');
                }).mouseout(function () {
                    $(this).find('.btn-group').addClass('hide');
                });

                $('#moveModal').on('show.bs.modal', function (event) {
                    const button = $(event.relatedTarget);
                    const name = button.data('name');
                    const modal = $(this);

                    modal.find('[name=path]').val(name)
                    modal.find('[name=new]').val(name)
                });

                $('#urlModal').on('show.bs.modal', function (event) {
                    const button = $(event.relatedTarget);
                    const url = button.data('url');

                    $(this).find('input').val(url)
                });

                $('.datatables').DataTable({
                    "order": [[4, "desc"]],
                    "columnDefs": [
                        {
                            "targets": 'no-sort',
                            "orderable": false,
                        },
                        {
                            'orderData': [5],
                            'targets': [4]
                        },
                        {
                            'targets': [5],
                            'visible': false,
                            'searchable': false
                        },
                        {
                            'orderData': [7],
                            'targets': [6]
                        },
                        {
                            'targets': [7],
                            'visible': false,
                            'searchable': false
                        }]
                });
            }

            $(function () {
                $(document).on("click", ".file-delete", function () {
                    const path = $(this).data('path');
                    const r = confirm("Are you sure to delete?");

                    if (r == true) {
                        $.ajax({
                            method: 'delete',
                            url: deleteUri,
                            data: {
                                'files[]': [path],
                            },
                            success: function () {
                                $.pjax.reload('#pjax-container');
                            }
                        });
                    }
                });

                $(document).on("submit", "#file-move", function (event) {
                    event.preventDefault();

                    const form = $(this);
                    const path = form.find('[name=path]').val();
                    const name = form.find('[name=new]').val();

                    $.ajax({
                        method: 'put',
                        url: moveUri,
                        data: {
                            path: path,
                            'new': name,
                        },
                        success: function (data) {
                            $.pjax.reload('#pjax-container');

                            if (typeof data === 'object') {
                                if (data.status) {
                                } else {
                                }
                            }
                        }
                    });
                    closeModal();
                });

                $(document).on("change", ".file-upload-form", function () {
                    $('.file-upload-form').submit();
                });

                $(document).on("submit", ".file-upload-form", function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function () {
                            $.pjax.reload('#pjax-container');
                        },
                        error: function (data) {
                        }
                    });
                });

                $(document).on("change", "#ImageBrowse", function () {
                    $("#imageUploadForm").submit();

                });

                $(document).on("submit", "#new-folder", function (event) {
                    event.preventDefault();

                    const formData = new FormData(this);

                    $.ajax({
                        method: 'POST',
                        url: newFolderUri,
                        data: formData,
                        success: function (data) {
                            $.pjax.reload('#pjax-container');

                            if (typeof data === 'object') {
                                if (data.status) {
                                } else {
                                }
                            }
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                    closeModal();
                });

                function closeModal() {
                    $("#moveModal").modal('toggle');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }

                $(document).on("click", ".btn-upload-select", function () {
                    $('.file-upload').trigger('click');
                });

                $(document).on("click", ".media-reload", function () {
                    $.pjax.reload('#pjax-container');
                });

                $(document).on("click", ".goto-url button", function () {
                    const path = $('.goto-url input').val();

                    $.pjax({container: '#pjax-container', url: `${indexUri}?path=${path}`});
                });

                $('.goto-url button').click(function () {

                });

                $(document).on("ifChanged", ".files-select-all", function () {
                    if (this.checked) {
                        $('.grid-row-checkbox').iCheck('check');
                    } else {
                        $('.grid-row-checkbox').iCheck('uncheck');
                    }
                });

                $(document).on("click", ".file-delete-multiple", function () {
                    const files = $(".file-select input:checked").map(function () {
                        return $(this).val();
                    }).toArray();

                    if (!files.length) {
                        return;
                    }

                    const r = confirm("Are you sure to delete?");

                    if (r == true) {
                        $.ajax({
                            method: 'delete',
                            url: deleteUri,
                            data: {
                                'files[]': files,
                            },
                            success: function () {
                                $.pjax.reload('#pjax-container');
                            }
                        });
                    }
                });

                $(document).on('ready pjax:end', function () {
                    reinventEvent();
                });

                reinventEvent();
            });
        }
    });

    /* universal report */

    function animateValue(obj, start = 0, end = null, duration = 2000) {
        if (obj) {
            // save starting text for later (and as a fallback text if JS not running and/or google)
            const textStarting = obj.innerHTML;

            // remove non-numeric from starting text if not specified
            end = end || parseInt(textStarting.replace(/\D/g, ""));

            const range = end - start;

            // no timer shorter than 50ms (not really visible any way)
            const minTimer = 50;

            // calc step time to show all interediate values
            let stepTime = Math.abs(Math.floor(duration / range));

            // never go below minTimer
            stepTime = Math.max(stepTime, minTimer);

            // get current time and calculate desired end time
            const startTime = new Date().getTime();
            const endTime = startTime + duration;
            let timer;

            function run() {
                const now = new Date().getTime();
                const remaining = Math.max((endTime - now) / duration, 0);
                const value = Math.round(end - (remaining * range));
                // replace numeric digits only in the original string
                obj.innerHTML = textStarting.replace(/([0-9]+)/g, value);

                if (value == end) {
                    clearInterval(stepTime);
                }
            }

            timer = setInterval(run, stepTime);
            run();
        }
    }

    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';
    const color = Chart.helpers.color;
    window.chartColors = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
    };

    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(',', '').replace(' ', '');

        const n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };

        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        let s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }

        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }

        return s.join(dec);
    }

    $(document).ready(function () {
        if ($('.universal-report').length) {
            animateValue(document.getElementById('increase-number-1'));
            animateValue(document.getElementById('increase-number-2'));
            animateValue(document.getElementById('increase-number-3'));
            animateValue(document.getElementById('increase-number-4'));
            animateValue(document.getElementById('increase-number-5'));
            animateValue(document.getElementById('increase-number-6'));

            //Pie chart

            const revenueElement = document.getElementById("RevenueSourcesChart");
            const RevenueSourcesChart = new Chart(revenueElement, {
                type: 'doughnut',
                data: {
                    labels: RevenueSourcesChartDataLabel,
                    datasets: [{
                        data: RevenueSourcesChartData,
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: true
                    },
                    cutoutPercentage: 0
                },
            });

            const paymentElement = document.getElementById("PaymentMethodChart");
            const PaymentMethodChart = new Chart(paymentElement, {
                type: 'doughnut',
                data: {
                    labels: ['Paypal', 'Stripe'],
                    datasets: [{
                        data: PaymentMethodChartData,
                        backgroundColor: ['#43b9cb', '#5a5c68'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: true
                    },
                    cutoutPercentage: 0
                },
            });

            const earningsElement = document.getElementById("EarningsReportsChart");
            const myLineChart = new Chart(earningsElement, {
                type: 'line',
                data: {
                    labels: EarningsReportsLabel,
                    datasets: [{
                        label: "Earnings",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: EarningsReportsData,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'date'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                // Include a dollar sign in the ticks
                                callback: function (value, index, values) {
                                    return currencyLabel  + number_format(value);
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function (tooltipItem, chart) {
                                const datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + currencyLabel + number_format(tooltipItem.yLabel);
                            }
                        }
                    }
                }
            });

            const monthlyElement = document.getElementById('MonthlyEarningsChart').getContext('2d');
            window.myBar = new Chart(monthlyElement, {
                type: 'bar',
                data: {
                    labels: MonthlyEarningsChartLabel,
                    datasets: [{
                        label: 'Earnings',
                        backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
                        borderColor: window.chartColors.red,
                        borderWidth: 1,
                        data: MonthlyEarningsChartEarningData,
                    },{
                        label: 'Orders',
                        backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
                        borderColor: window.chartColors.green,
                        borderWidth: 1,
                        data: MonthlyEarningsChartOrdersData,
                    }]

                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                    }
                }
            });
        }
    });
    $(document).on('click', ('[data-action="mark-paid"]'), function () {
        const element = $(this);

        $.ajax({
            method: 'POST',
            url: window.location,
            data: {
                action: element.attr('data-init') ? 'unPaid' : 'markPaid',
                id: element.attr('data-id')
            },
            success: function () {
                if (element.attr('data-init')) {
                    element.html('Mark as Paid').addClass('btn-secondary').removeClass('btn-success').removeAttr('data-init', true);
                } else {
                    element.html('Paid').removeClass('btn-secondary').addClass('btn-success').attr('data-init', true);
                }
            }
        });
    });

    $(document).on('click', '[data-action="decline-request"]', function () {
        const el = $(this);

        bootbox.confirm({
            title: "Decline Payment Request?",
            message: "This action will cancel the request and return the request's amount to user's balance. Do you want to process?",
            centerVertical: true,
            callback: function (result) {
                if(result) {
                    $.ajax({
                        method: 'POST',
                        url: window.location,
                        data: {
                            action: 'decline',
                            id: el.attr('data-id')
                        },
                        success: function () {
                            el.parents('tr').remove();
                        }
                    });
                }
            }
        });
    });
});
