//update form fields
jQuery(document).ready(function ($) {

    $("#mailflow-addnew-fields").click(function () {
        $("#mailflow_admin_field_list tbody").append($("#mailflow_admin_field_list_tpl .mailflow-input-container:first").clone());
    });

    $("#mailflow-addnew-tag").click(function () {
        var tpl = $("#mailflow-tag-select-tpl tr").clone();
        var randomid = uniqid("newtag");
        $(tpl).find("select").attr("id", randomid).attr('name', "mailflow-tags[" + randomid + "][pages][]");
        $(tpl).find(".mailflow-tag").attr('name', "mailflow-tags[" + randomid + "][tag]");
        $("#mailflow_admin_tags_list tbody").append(tpl);
      //  $("#" + randomid).select2();

    });

    $("#mailflow_admin_tags_list tbody").on("click", ".mailflow-feild-delete-button", function () {
        $(this).closest("tr").remove();
    });
    $("#mailflow_admin_field_list tbody").on("click", ".mailflow-feild-delete-button", function () {
        $(this).closest("tr").remove();
    });

    //using select2 for tags
    $(".mailflow-tags-pagelist-edit").select2();
    $(".mailflow-role-tags").select2({
        tags: true
    });
    
    $(".mailflow-form-tags").select2({
        tags: true
    });
});

function uniqid(prefix, more_entropy) {
    if (typeof prefix === 'undefined') {
        prefix = '';
    }

    var retId;
    var formatSeed = function (seed, reqWidth) {
        seed = parseInt(seed, 10)
                .toString(16); // to hex str
        if (reqWidth < seed.length) { // so long we split
            return seed.slice(seed.length - reqWidth);
        }
        if (reqWidth > seed.length) { // so short we pad
            return Array(1 + (reqWidth - seed.length))
                    .join('0') + seed;
        }
        return seed;
    };

    // BEGIN REDUNDANT
    if (!this.php_js) {
        this.php_js = {};
    }
    // END REDUNDANT
    if (!this.php_js.uniqidSeed) { // init seed with big random int
        this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
    }
    this.php_js.uniqidSeed++;

    retId = prefix; // start with prefix, add current milliseconds hex string
    retId += formatSeed(parseInt(new Date()
            .getTime() / 1000, 10), 8);
    retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
    if (more_entropy) {
        // for more entropy we add a float lower to 10
        retId += (Math.random() * 10)
                .toFixed(8)
                .toString();
    }

    return retId;
}