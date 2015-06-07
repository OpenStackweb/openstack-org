var React = require('react');

var PresentationList = React.createClass({displayName: 'PresentationList',

  getDefaultProps: function() {
    return {
      presentations: [],
      selected: null,
      onSelect: function () {},
      hasMore: false,
      onLoadMore: function () {},
      loadMoreText: 'Load more...'
    };
  },


  getInitialState: function() {
    return {
      height: 'auto' 
    }
  },


  componentDidMount: function() {
    window.addEventListener('resize', this._resizeListener);
    this._resizeListener();
  },


  componentDidUnmount: function() {
    window.removeEventListener('resize', this._resizeListener);
  },


  _resizeListener: function (e) {
    var height = isNaN(window.innerHeight) ? window.clientHeight : window.innerHeight;
    this.setState({
      height: height-200
    });
  },


	render: function() {    
		return (
          <ul className="presentation-list" style={{height: this.state.height}}>
            {this.props.presentations.map(function(pres) {
              var klass = (this.props.selected == pres.id) ? 'active' : (pres.user_vote ? 'completed' : 'upcoming');
              return (
                <li key={pres.id} className={klass} onClick={this.props.onSelect.bind(null, pres)}>
                  <a>
                    {pres.title}
                  </a>
                </li>
              );

            }.bind(this))}
            {this.props.hasMore &&
              <li className="load-more"><a onClick={this.props.onLoadMore}>{this.props.loadMoreText}</a></li>
            }
          </ul>			
		);
	}

});

module.exports = PresentationList;	