var $collectionHolder;var $addTagLink=$('<a href="#" class="add_tag_link">Add a tag</a>');var $newLinkLi=$("<li></li>").append($addTagLink);$(document).ready(function(){alert("helllo");$collectionHolder=$("ul.tags");$collectionHolder.find("li").each(function(){addTagFormDeleteLink($(this))});$collectionHolder.append($newLinkLi);$collectionHolder.data("index",$collectionHolder.find(":input").length);$addTagLink.on("click",function(e){e.preventDefault();addTagForm($collectionHolder,$newLinkLi)})});function addTagForm($collectionHolder,$newLinkLi){var prototype=$collectionHolder.data("prototype");var index=$collectionHolder.data("index");var newForm=prototype.replace(/__name__/g,index);$collectionHolder.data("index",index+1);var $newFormLi=$("<li></li>").append(newForm);$newLinkLi.before($newFormLi);addTagFormDeleteLink($newFormLi)}function addTagFormDeleteLink($tagFormLi){var $removeFormA=$('<a href="#">delete this tag</a>');$tagFormLi.append($removeFormA);$removeFormA.on("click",function(e){e.preventDefault();$tagFormLi.remove()})}