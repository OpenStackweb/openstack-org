/**
 * @jsx React.DOM
 */
var React = require('react');
var DropdownSelector = require('../components/DropdownSelector');
var DropdownItem = require('../components/DropdownItem');
var Router = require('react-router');
var Backend = require('../store/Backend');
var assign = require('object-assign');
var PresentationList = require('../components/PresentationList');
var SearchBar = require('../components/SearchBar');

module.exports = React.createClass({displayName:'Main',


  mixins: [Router.Navigation, Router.State],


  _latestParams: null,

  _page: -1,

  _perPage: 50,

  getInitialState: function() {
    return {
      keyword: null,
      activeCategory: null,
      hasMore: false 
    };
  },


  componentDidMount: function() {
    document.addEventListener('keyup', this._keyListener);
  },


  componentWillUnmount: function() {
    document.removeEventListener('keyup', this._keyListener);
  },


  componentWillMount: function() {
    this._processParams();
  },


  componentWillReceiveProps: function(nextProps) {     
    this._processParams();
  },


  _keyListener: function (e) {
    if(e.keyCode === 37) return this._navigateAdjacentPresentation(-1);
    if(e.keyCode === 39) return this._navigateAdjacentPresentation(1);
  },


  _updateQueryString: function (params) {
    var query = assign({}, 
      this.getQuery(),
      params
    );

    for(var i in query) {
      if(query[i] === null) delete query[i];
    }

    return this.transitionTo(
      this.getPathname(), 
      {}, 
      query
    );
  },


  _handleSearch: function (text) {
    if(!text.length) text = null;
    return this._updateQueryString({
      keyword: text
    });
  },


  _handleCategoryChange: function (key) {    
    if(isNaN(parseInt(key))) key = null;
    return this._updateQueryString({
      category: key
    });
  },


  _handlePresentationSelected: function (pres) {
    this.transitionTo('detail', {presentationID: pres.id}, this.getQuery());
  },


  _paramsHaveChanged: function () {
      return !this._latestParams || (JSON.stringify(this._latestParams) !== JSON.stringify(this.getQuery()));
  },

  _processParams: function () {
    this.setState({
      activeCategory: Backend.getCategoryByID(this.getQuery().category),
      keyword: this.getQuery().keyword
    })
    
    if(this._paramsHaveChanged()) {
      this._latestParams = this.getQuery();
      this._page = 0;
      Backend.resetPresentations();
      this._loadPresentations();
    }
  },


  _loadPresentations: function () {    
    var params = assign({}, this._latestParams, {
      page: this._page++,
      limit: this._perPage
    });

    Backend.getPresentations(params);
  },


  _navigateAdjacentPresentation: function (adder) {
    var store = this.props.store,
        active = store.activePresentation.getValue(),
        index, adjacent;

    if(!active) return;

    index = store.presentations.findIndex(function (p) {
      return p.id.getValue() == active.id;
    });

    if(adjacent = store.presentations[index+adder]) {
      return this._handlePresentationSelected(adjacent.getValue());
    }
  },


  _isReady: function () {
      var store = this.props.store;
      return !!(store.summit.categories.getValue() && store.presentations.getValue());
  },


  render: function() {
    var store = this.props.store,
        categories = store.summit.categories,
        activeCategoryData = this.state.activeCategory ? this.state.activeCategory.getValue() : { id: null, title: 'All categories' },
        activePresentationID = store.activePresentation.getValue() ? store.activePresentation.id.getValue() : null,
        keyword = this.state.keyword,
        pagination = store.presentationPagination.getValue(),
        nextPageCount = Math.min(pagination.remaining, this._perPage);

        if(!this._isReady()) {
          return <div className="loading">Loading...</div>                    
        }
    return (
      <div>
        <div className="col-lg-3 col-md-3 col-sm-3 voting-sidebar">
          <div className="voting-app-details-link">
            <a href="#">
              More About The Vancouver Summit
            </a>
          </div>
          <SearchBar className="text voting-search-input" placeholder="Search" id="filter-field" onUpdate={this._handleSearch} initialText={keyword} />
          <DropdownSelector onChange={this._handleCategoryChange} defaultSelection={activeCategoryData.id}>
            <DropdownItem val={null}>All Categories</DropdownItem>
            <li className="divider" />
            {categories.getValue().map(function(cat, i) {
              return <DropdownItem key={cat.id} val={cat.id}>{cat.title}</DropdownItem>
            })}
          </DropdownSelector>


          <h5>
            {this.getQuery().keyword && 'Search for "'+keyword+'" in '}
            {activeCategoryData.title} <span>({pagination.total} results)</span> <span>({store.presentations.count()} showing)</span>
            </h5>
          <PresentationList 
            presentations={store.presentations.getValue()}
            selected={activePresentationID}
            onSelect={this._handlePresentationSelected}
            hasMore={pagination.hasMore}
            onLoadMore={this._loadPresentations}
            loadMoreText={"Load " + nextPageCount + " more..."} />
        </div>
        <div className="col-lg-9 col-md-9 col-sm-9 voting-content-body-wrapper">
          <Router.RouteHandler store={this.props.store} />
        </div>
      </div>
    );
  }
});