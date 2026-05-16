$(function () {
    var $renameModal = $("#js-confirm-rename");

    function renameFile($renameModalButton) {
        $("#rename_f_name").val($renameModalButton.data("name"));
        $("#rename_f_extension").val($renameModalButton.data("extension"));
        $renameModal.find("form").attr("action", $renameModalButton.data("href"));
    }

    function deleteFile($deleteModalButton) {
        $("#js-confirm-delete").find("form").attr("action", $deleteModalButton.data("href"));
    }

    function initTree(treedata) {
        $("#tree")
            .jstree({
                core: {
                    data: treedata,
                    check_callback: true,
                },
            })
            .bind("changed.jstree", function (e, data) {
                if (data.node) {
                    document.location = data.node.a_attr.href;
                }
            })
            .bind("loaded.jstree", function () {
                $("#tree")
                    .find(".label")
                    .removeClass("label label-default")
                    .addClass("badge badge-sm badge-outline rounded-pill text-primary")
                    .css("top", "-3px")
                    .css("position", "relative");
            });
    }

    if (tree === true) {
        $("#tree-block").stick_in_parent();
        initTree(treedata);
    }

    $(document)
        .on("click", "#select-all", function () {
            $("#form-multiple-delete").find(":checkbox").prop("checked", $(this).is(":checked"));
        })
        .on("click", ".js-delete-modal", function () {
            deleteFile($(this));
        })
        .on("click", ".js-rename-modal", function () {
            renameFile($(this));
        })
        .on("click", "#js-delete-multiple-modal", function () {
            var $multipleDelete = $("#form-multiple-delete").serialize();
            if ($multipleDelete) {
                var href = urldelete + "&" + $multipleDelete;
                $("#js-confirm-delete").find("form").attr("action", href);
            }
        })
        .on("click", "#form-multiple-delete :checkbox", function () {
            var $jsDeleteMultipleModal = $("#js-delete-multiple-modal");
            if ($(".checkbox").is(":checked")) {
                $jsDeleteMultipleModal.removeClass("d-none");
            } else {
                $jsDeleteMultipleModal.addClass("d-none");
            }
        });

    $renameModal.on("shown.bs.modal", function () {
        $("#rename_f_name")
            .select()
            .one("mouseup", function (e) {
                e.preventDefault();
            });
    });
    $("#addFolder").on("shown.bs.modal", function () {
        $("#rename_name")
            .select()
            .one("mouseup", function (e) {
                e.preventDefault();
            });
    });

    if (moduleName === "tiny") {
        $("#form-multiple-delete").on("click", ".select", function () {
            var windowManager =
                top !== undefined && top.tinymceWindowManager !== undefined ? top.tinymceWindowManager : "";

            if (windowManager !== "") {
                if (top.tinymceCallBackURL !== undefined) {
                    top.tinymceCallBackURL = $(this).attr("data-path");
                }
                windowManager.close();
            } else {
                var args = top.tinymce.activeEditor.windowManager.getParams();
                var input = args.input;
                var editorDocument = args.window.document;
                var divInputSplit = editorDocument.getElementById(input).parentNode.id.split("_");

                editorDocument.getElementById(input).value = $(this).attr("data-path");

                var baseId = divInputSplit[0] + "_";
                var baseInt = parseInt(divInputSplit[1], 10);
                var divWidth = baseId + (baseInt + 3);
                var divHeight = baseId + (baseInt + 5);

                editorDocument.getElementById(divWidth).value = $(this).attr("data-width");
                editorDocument.getElementById(divHeight).value = $(this).attr("data-height");

                top.tinymce.activeEditor.windowManager.close();
            }
        });
    }

    if (moduleName === "ckeditor") {
        $("#form-multiple-delete").on("click", ".select", function () {
            var regex = new RegExp("[\\?&]CKEditorFuncNum=([^&#]*)");
            var match = regex.exec(location.search);
            if (!match) {
                return;
            }
            var funcNum = match[1];
            var fileUrl = $(this).attr("data-path");
            window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
            window.close();
        });
    }

    function displayError(msg) {
        displayAlert("error", msg);
    }

    function displaySuccess(msg) {
        displayAlert("success", msg);
    }

    $("#fileupload")
        .fileupload({
            dataType: "json",
            processQueue: false,
            dropZone: $("#dropzone"),
        })
        .on("fileuploaddone", function (e, data) {
            $.each(data.result.files, function (index, file) {
                const fileName = $("<strong>").text(file.name).html();
                if (file.url) {
                    displaySuccess(fileName + " " + successMessage);
                    $.ajax({
                        dataType: "json",
                        url: url,
                        type: "GET",
                    })
                        .done(function (data) {
                            $("#form-multiple-delete").html(data.data);

                            lazy();

                            if (tree === true) {
                                $("#tree").data("jstree", false).empty();
                                initTree(data.treeData);
                            }

                            $("#select-all").prop("checked", false);
                            $("#js-delete-multiple-modal").addClass("d-none");
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            displayError("<strong>Ajax call error :</strong> " + jqXHR.status + " " + errorThrown);
                        });
                } else if (file.error) {
                    displayError(fileName + " " + file.error);
                }
            });
        })
        .on("fileuploadfail", function (e, data) {
            $.each(data.files, function () {
                displayError("File upload failed.");
            });
        })
        .on("fileuploadprogressall", function (e, data) {
            if (e.isDefaultPrevented()) {
                return false;
            }
            var progress = Math.floor((data.loaded / data.total) * 100);

            $(".progress-bar")
                .removeClass("notransition")
                .attr("aria-valuenow", progress)
                .css("width", progress + "%");
        })
        .on("fileuploadstop", function (e) {
            if (e.isDefaultPrevented()) {
                return false;
            }
            $(".progress-bar")
                .addClass("notransition")
                .attr("aria-valuenow", 0)
                .css("width", "0%");
        });

    function lazy() {
        $(".lazy").Lazy({});
    }

    lazy();

    $("#search").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#form-multiple-delete .file-wrapper").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
