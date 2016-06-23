import React from 'react';

export default ({
    title,
    description,
    notes,
    active,
    muted,
    className
}) => (
   <div className={`feed-element ${active ? 'active' : ''} ${muted ? 'muted' : ''} ${className}`}>
      <div>
         <strong>{title}</strong>
         <div>{description}</div>
         <small className="text-navy">{notes}</small>
      </div>
   </div>
);