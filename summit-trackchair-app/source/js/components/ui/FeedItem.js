import React from 'react';

export default ({
    title,
    description,
    notes,
    active,
    muted
}) => (
   <div className={`feed-element ${active ? 'active' : ''} ${muted ? 'muted' : ''}`}>
      <div>
         <strong>{title}</strong>
         <div>{description}</div>
         <small className="pull-right text-navy">{notes}</small>
      </div>
   </div>
);