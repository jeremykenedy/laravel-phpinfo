(function () {
    'use strict';

    function initialize(page) {
        if (page.getAttribute('data-phpinfo-ready')) {
            return;
        }

        page.setAttribute('data-phpinfo-ready', 'true');
        var controls = page.querySelector('[data-phpinfo-controls]');
        var theme = page.querySelector('[data-phpinfo-theme]');
        var search = page.querySelector('[data-phpinfo-search]');
        var empty = page.querySelector('[data-phpinfo-empty]');
        var output = page.querySelector('.php-info');
        var tables = Array.prototype.slice.call(output.querySelectorAll('table'));
        var selected = page.getAttribute('data-theme');

        try {
            var saved = window.localStorage.getItem('laravelPhpInfo.theme');
            if (['system', 'light', 'dark'].indexOf(saved) !== -1) {
                selected = saved;
            }
        } catch (error) {
        }

        page.setAttribute('data-theme', selected);
        theme.value = selected;
        controls.hidden = false;

        theme.addEventListener('change', function () {
            page.setAttribute('data-theme', theme.value);
            try {
                window.localStorage.setItem('laravelPhpInfo.theme', theme.value);
            } catch (error) {
            }
        });

        if (!tables.length) {
            search.parentNode.hidden = true;
            return;
        }

        var sections = tables.map(function (table) {
            var heading = table.previousElementSibling;
            if (!heading || !/^H[1-6]$/.test(heading.tagName)) {
                heading = null;
            }

            return { table: table, heading: heading, rows: Array.prototype.slice.call(table.rows) };
        });

        search.addEventListener('input', function () {
            var query = search.value.trim().toLocaleLowerCase();
            var found = false;

            sections.forEach(function (section) {
                var sectionMatch = section.heading && section.heading.textContent.toLocaleLowerCase().indexOf(query) !== -1;
                var matches = false;

                section.rows.forEach(function (row) {
                    var header = row.querySelector('th') !== null;
                    var match = !query || sectionMatch || row.textContent.toLocaleLowerCase().indexOf(query) !== -1;
                    row.hidden = !header && !match;
                    matches = matches || (!header && match);
                });

                section.table.hidden = !matches;
                if (section.heading) {
                    section.heading.hidden = !matches;
                }
                found = found || matches;
            });

            empty.hidden = found;
        });
    }

    function initializePages() {
        Array.prototype.forEach.call(document.querySelectorAll('[data-phpinfo]'), initialize);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePages);
    } else {
        initializePages();
    }
    document.addEventListener('livewire:navigated', initializePages);
}());
