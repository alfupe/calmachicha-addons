class FilteredGridHandlerClass extends elementorModules.frontend.handlers.Base {
    constructor(props) {
        super(props);
        this.$grid = this.initIsotopeGrid();
    }

    initIsotopeGrid = () => {
        const $container = jQuery('.cca-filtered-grid');
        const gutter = this.getElementSettings('gutter');
        const container = document.querySelector('.cca-filtered-grid');
        container.style.setProperty('--gutter', `${gutter.size}${gutter.unit}`);
        console.log('gutter', gutter);
        const $grid = $container.isotope({
            itemSelector: '.cca-filtered-grid__item',
            layoutMode: 'masonry',
            percentPosition: true,
            masonry: {
                columnWidth: '.cca-filtered-grid__item',
                gutter: gutter.size
            }
        });

        // layout Isotope after each image loads
        $grid.imagesLoaded().progress(function() {
            $grid.isotope('layout');
        });

        return $grid;
    };

    getDefaultSettings() {
        return {
            selectors: {
                filter: '.cca-filtered-grid-filters__filter'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $filter: this.$element.find(selectors.filter)
        };
    }

    bindEvents() {
        this.elements.$filter.on('click', this.onFilterClick.bind(this));
    }

    onFilterClick(event) {
        event.preventDefault();
        const $currentFilter = jQuery(event.target);
        const $filter = jQuery('.cca-filtered-grid-filters__filter');
        const filterValue = $currentFilter.attr('data-filter');
        $filter.removeClass('cca-filtered-grid-filters__filter--is-active');

        if (filterValue === $currentFilter.attr('data-filter')) {
            $currentFilter.addClass('cca-filtered-grid-filters__filter--is-active');
        }

        this.$grid.isotope({
            filter: filterValue
        });
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    const addHandler = $element => {
        elementorFrontend.elementsHandler.addHandler(FilteredGridHandlerClass, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/cca-filtered-grid.default', addHandler);
});
