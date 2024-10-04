!function(a){Craft.RichTextInput=Garnish.Base.extend({id:null,linkOptions:null,assetSources:null,elementLocale:null,redactorConfig:null,$textarea:null,redactor:null,linkOptionModals:null,init:function(t){if(this.id=t.id,this.linkOptions=t.linkOptions,this.assetSources=t.assetSources,this.transforms=t.transforms,this.elementLocale=t.elementLocale,this.redactorConfig=t.redactorConfig,this.linkOptionModals=[],this.redactorConfig.lang||(this.redactorConfig.lang=t.redactorLang),this.redactorConfig.direction||(this.redactorConfig.direction=t.direction||Craft.orientation),this.redactorConfig.imageUpload=!0,this.redactorConfig.fileUpload=!0,this.redactorConfig.dragImageUpload=!1,this.redactorConfig.dragFileUpload=!1,typeof this.redactorConfig.plugins!=typeof[]&&(this.redactorConfig.plugins=[]),this.redactorConfig.buttons){var e;-1!==(e=a.inArray("html",this.redactorConfig.buttons))&&(this.redactorConfig.buttons.splice(e,1),this.redactorConfig.plugins.unshift("source")),-1!==(e=a.inArray("formatting",this.redactorConfig.buttons))&&this.redactorConfig.buttons.splice(e,1,"format");for(var i,o=["unorderedlist","orderedlist","undent","indent"],r=0;r<o.length;r++)-1!==(e=a.inArray(o[r],this.redactorConfig.buttons))&&(this.redactorConfig.buttons.splice(e,1),(void 0===i||e<i)&&(i=e));void 0!==i&&this.redactorConfig.buttons.splice(i,0,"lists")}this.redactorConfig.callbacks={init:Craft.RichTextInput.handleRedactorInit},this.$textarea=a("#"+this.id),this.initRedactor(),void 0!==Craft.livePreview&&(Craft.livePreview.on("beforeEnter beforeExit",a.proxy(function(){this.redactor.core.destroy()},this)),Craft.livePreview.on("enter slideOut",a.proxy(function(){this.initRedactor()},this)))},mergeCallbacks:function(t,e){return function(){t.apply(this,arguments),e.apply(this,arguments)}},initRedactor:function(){if(this.redactorConfig.toolbarFixed){var t=this.$textarea.closest(".lp-editor");t.length&&(this.redactorConfig.toolbarFixedTarget=t)}(Craft.RichTextInput.currentInstance=this).$textarea.redactor(this.redactorConfig),delete Craft.RichTextInput.currentInstance},onInitRedactor:function(t){this.redactor=t,this.redactor.opts.toolbar&&this.customizeToolbar(),this.leaveFullscreetOnSaveShortcut(),this.redactor.core.editor().on("focus",a.proxy(this,"onEditorFocus")).on("blur",a.proxy(this,"onEditorBlur")),this.redactor.opts.toolbarFixed&&!Craft.RichTextInput.scrollPageOnReady&&(Garnish.$doc.ready(function(){Garnish.$doc.trigger("scroll")}),Craft.RichTextInput.scrollPageOnReady=!0)},customizeToolbar:function(){if(this.assetSources.length){var t=this.replaceRedactorButton("image",this.redactor.lang.get("image")),e=this.replaceRedactorButton("file",this.redactor.lang.get("file"));t&&this.redactor.button.addCallback(t,a.proxy(this,"onImageButtonClick")),e&&this.redactor.button.addCallback(e,a.proxy(this,"onFileButtonClick"))}else this.redactor.button.remove("image"),this.redactor.button.remove("file");if(this.linkOptions.length){var i=this.replaceRedactorButton("link",this.redactor.lang.get("link"));if(i){for(var o={},r=0;r<this.linkOptions.length;r++)o["link_option"+r]={title:this.linkOptions[r].optionTitle,func:a.proxy(this,"onLinkOptionClick",r)};a.extend(o,{link:{title:this.redactor.lang.get("link-insert"),func:"link.show",observe:{element:"a",in:{title:this.redactor.lang.get("link-edit")},out:{title:this.redactor.lang.get("link-insert")}}},unlink:{title:this.redactor.lang.get("unlink"),func:"link.unlink",observe:{element:"a",out:{attr:{class:"redactor-dropdown-link-inactive","aria-disabled":!0}}}}}),this.redactor.button.addDropdown(i,o)}}},onImageButtonClick:function(){this.redactor.selection.save(),void 0===this.assetSelectionModal?this.assetSelectionModal=Craft.createElementSelectorModal("Asset",{storageKey:"RichTextFieldType.ChooseImage",multiSelect:!0,sources:this.assetSources,criteria:{locale:this.elementLocale,kind:"image"},onSelect:a.proxy(function(t,e){if(t.length){this.redactor.selection.restore();for(var i=0;i<t.length;i++){var o=t[i],r=o.url+"#asset:"+o.id;e&&(r+=":"+e),this.redactor.insert.node(a("<"+this.redactor.opts.imageTag+'><img src="'+r+'" /></>')[0]),this.redactor.code.sync()}this.redactor.observe.images()}},this),closeOtherModals:!1,transforms:this.transforms}):this.assetSelectionModal.show()},onFileButtonClick:function(){this.redactor.selection.save(),void 0===this.assetLinkSelectionModal?this.assetLinkSelectionModal=Craft.createElementSelectorModal("Asset",{storageKey:"RichTextFieldType.LinkToAsset",sources:this.assetSources,criteria:{locale:this.elementLocale},onSelect:a.proxy(function(t){if(t.length){this.redactor.selection.restore();var e=t[0],i=e.url+"#asset:"+e.id,o=this.redactor.selection.text(),r=0<o.length?o:e.label;this.redactor.insert.node(a('<a href="'+i+'">'+r+"</a>")[0]),this.redactor.code.sync()}},this),closeOtherModals:!1,transforms:this.transforms}):this.assetLinkSelectionModal.show()},onLinkOptionClick:function(t){if(this.redactor.selection.save(),void 0===this.linkOptionModals[t]){var s=this.linkOptions[t];this.linkOptionModals[t]=Craft.createElementSelectorModal(s.elementType,{storageKey:s.storageKey||"RichTextFieldType.LinkTo"+s.elementType,sources:s.sources,criteria:a.extend({locale:this.elementLocale},s.criteria),onSelect:a.proxy(function(t){if(t.length){this.redactor.selection.restore();var e=t[0],i=s.elementType.replace(/^\w|_\w/g,function(t){return t.toLowerCase()}),o=e.url+"#"+i+":"+e.id,r=this.redactor.selection.text(),n=0<r.length?r:e.label;this.redactor.insert.node(a('<a href="'+o+'">'+n+"</a>")[0]),this.redactor.code.sync()}},this),closeOtherModals:!1})}else this.linkOptionModals[t].show()},onEditorFocus:function(){this.redactor.core.box().addClass("focus")},onEditorBlur:function(){this.redactor.core.box().removeClass("focus")},leaveFullscreetOnSaveShortcut:function(){void 0!==this.redactor.fullscreen&&"function"==typeof this.redactor.fullscreen.disable&&Craft.cp.on("beforeSaveShortcut",a.proxy(function(){this.redactor.fullscreen.isOpen&&this.redactor.fullscreen.disable()},this))},replaceRedactorButton:function(t,e){if(this.redactor.button.get(t).length){var i=this.redactor.button.addAfter(t,t+"_placeholder");this.redactor.button.remove(t);var o=this.redactor.button.build(t,{title:e,icon:!0});return i.parent().after(a("<li>").append(o)),i.remove(),o}}},{handleRedactorInit:function(){Craft.RichTextInput.currentInstance.onInitRedactor(this)}})}(jQuery);
//# sourceMappingURL=RichTextInput.js.map