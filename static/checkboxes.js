$(function(){

    $("[check_to]").each(function(k,el){
        
        $(el).click(function(){
            target = $(this).attr("check_to");
            $("[check_from="+target+"],[check_from="+target+"] [type=checkbox]").prop("checked",$(this).prop("checked"));            
        });

        //verifica se todos estao marcados
        target = $(this).attr("check_to");
        todosMarcados = true;
        $("input[check_from="+target+"],[check_from="+target+"] [type=checkbox]").each(function(i, el2){
            if ($(el2).prop("checked") == false && el2 != el){
                todosMarcados = false;
                return;
            }
        });
        $(el).prop("checked", todosMarcados);
        

        
    });
});