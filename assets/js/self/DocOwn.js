$(function() {
    /*
     * 输入框
     */
    $('#editor2').css({'height':'200px'}).ace_wysiwyg({
        toolbar_place: function(toolbar) {
            return $(this).closest('.widget-box')
                   .find('.widget-header').prepend(toolbar)
                   .find('.wysiwyg-toolbar').addClass('inline');
        },
        toolbar:
        [
            'bold',
            {name:'italic' , title:'Change Title!', icon: 'ace-icon fa fa-leaf'},
            'strikethrough',
            null,
            'insertunorderedlist',
            'insertorderedlist',
            null,
            'justifyleft',
            'justifycenter',
            'justifyright'
        ],
        speech_button: false
    });
    
    /*
     * 新建文档
     */
    var demo1 = $('select[name="duallistbox_demo1[]"]').bootstrapDualListbox({infoTextFiltered: '<span class="label label-purple label-lg">Filtered</span>'});
    var demo1 = $('select[name="duallistbox_demo1[]"]').bootstrapDualListbox({infoTextFiltered: '<span class="label label-purple label-lg">Filtered</span>'});
    var container1 = demo1.bootstrapDualListbox('getContainer');
    container1.find('.btn').addClass('btn-white btn-info btn-bold');

    /**var setRatingColors = function() {
        $(this).find('.star-on-png,.star-half-png').addClass('orange2').removeClass('grey');
        $(this).find('.star-off-png').removeClass('orange2').addClass('grey');
    }*/
    $('.rating').raty({
        'cancel' : true,
        'half': true,
        'starType' : 'i'
        /**,
        
        'click': function() {
            setRatingColors.call(this);
        },
        'mouseover': function() {
            setRatingColors.call(this);
        },
        'mouseout': function() {
            setRatingColors.call(this);
        }*/
    })//.find('i:not(.star-raty)').addClass('grey');
    
    
    
    //////////////////
    //select2
    $('.select2').css('width','200px').select2({allowClear:true})
    $('#select2-multiple-style .btn').on('click', function(e){
        var target = $(this).find('input[type=radio]');
        var which = parseInt(target.val());
        if(which == 2) $('.select2').addClass('tag-input-style');
         else $('.select2').removeClass('tag-input-style');
    });
    
    //in ajax mode, remove remaining elements before leaving page
    $(document).one('ajaxloadstart.page', function(e) {
        $('[class*=select2]').remove();
        $('select[name="duallistbox_demo1[]"]').bootstrapDualListbox('destroy');
        $('.rating').raty('destroy');
        $('.multiselect').multiselect('destroy');
    });
})
