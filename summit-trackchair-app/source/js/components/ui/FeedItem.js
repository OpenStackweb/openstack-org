import React from 'react';

export default ({
    title,
    description,
    notes,
    active,
    muted,
    rank,
    className
}) => (
   <div className={`feed-element ${active ? 'active' : ''} ${muted ? 'muted' : ''} ${className}`}>
      <div>
         <strong>{title}</strong>
         <div>{description}</div>
         <small className="text-navy">{notes}</small>
         {rank !== undefined &&
         	<span className="rank">{rank}</span>
         }
      </div>
   </div>
);