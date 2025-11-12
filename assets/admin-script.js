document.addEventListener("DOMContentLoaded", function(){

    /* Group tabs */
    document.querySelectorAll(".acp-report-tabs").forEach(function(container){
        container.querySelectorAll(".acp-report-tabs__nav-btn").forEach(function(button){
            button.addEventListener("click", function(){
                var target=button.getAttribute("data-tab");
                container.querySelectorAll(".acp-report-tabs__nav-btn").forEach(function(btn){
                    btn.classList.toggle("is-active", btn === button);
                });
                container.querySelectorAll(".acp-report-tabs__panel").forEach(function(panel){
                    panel.classList.toggle("is-active", panel.getAttribute("data-tab") === target);
                });
            });
        });
    });

    /* Custom Select inputs */
    const acp_selects = document.querySelectorAll('.acp-csel');

    if(acp_selects.length){    
        acp_selects.forEach(csel => {
            const id_base = csel.id;
            if(!id_base) return;

            const root = csel;
            const hidden = document.getElementById(id_base + '-hidden');
            const custom_key_value = document.getElementById(id_base + '-custom');
            const value = hidden ? hidden.value : '';

            if(custom_key_value){
                if(value === 'attr' || value === 'meta' || value === 'static'){
                    custom_key_value.style.display = 'inline-block';
                } else {
                    custom_key_value.style.display = 'none';
                }

                if(value === 'static'){
                    custom_key_value.placeholder = "Enter value here";
                }else{
                    custom_key_value.placeholder = "Enter custom key";
                }
            }

            function selectOption(el){
                if(!el) return;

                root.querySelectorAll('.acp-csel-option').forEach(o => {
                    o.classList.remove('is-selected');
                    o.setAttribute('aria-selected','false');
                });

                el.classList.add('is-selected');
                el.setAttribute('aria-selected','true');

                const selected_value = el.dataset.value || '';

                hidden.value = selected_value;

                if(custom_key_value){
                    if(selected_value === 'attr' || selected_value === 'meta' || selected_value === 'static'){
                        custom_key_value.style.display = 'inline-block';
                    } else {
                        custom_key_value.style.display = 'none';
                    }

                    if(selected_value === 'static'){
                        custom_key_value.placeholder = "Enter value here";
                    }else{
                        custom_key_value.placeholder = "Enter custom key";
                    }
                }
            }

            root.addEventListener('click', function(e){
                const opt = e.target.closest('.acp-csel-option');
                if (opt && root.contains(opt)) selectOption(opt);
            });

        });
    }

    /* Help boxes */
    const acp_helps = document.querySelectorAll('.acp-help');

    if(acp_helps.length){
        acp_helps.forEach(acp_help => {
            const title = acp_help.getAttribute('acp-title');
            if (!title) return;

            acp_help.addEventListener('mouseenter', function(e){
                const tooltip = document.createElement('div');
                tooltip.className = 'acp-tooltip';
                tooltip.innerText = title;
                document.body.appendChild(tooltip);
                const rect = acp_help.getBoundingClientRect();
                const trect = tooltip.getBoundingClientRect();
                let top = rect.top - trect.height - 8;
                if (top < 0) top = rect.bottom + 8;
                let left = rect.left + (rect.width - trect.width) / 2;
                if (left < 0) left = 8;
                if (left + trect.width > window.innerWidth) left = window.innerWidth - trect.width - 8;
                tooltip.style.top = top + 'px';
                tooltip.style.left = left + 'px';
                acp_help._tooltip = tooltip;
            });
            acp_help.addEventListener('mouseleave', function(e){
                if (acp_help._tooltip){
                    document.body.removeChild(acp_help._tooltip);
                    acp_help._tooltip = null;
                }
            });
        });
    }
});