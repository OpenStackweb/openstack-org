import React from 'react';

export default ({
	presentation
}) => (
<div>
  <div className="voting-presentation-title">
     <h5>Title</h5>
     <h3>{presentation.title}</h3>
  </div>
  <div className="voting-presentation-track">
     <h5>Track</h5>
     <p>{presentation.category}</p>
  </div>
</div>
);