import React from 'react';

export default ({
    title,
    description,
    notes,
    active,
    muted,
    rank,
    className,
    lightning,
    lightning_wannabe
}) => (
   <div className={`feed-element ${active ? 'active' : ''} ${muted ? 'muted' : ''} ${className}`}>
      <div>
         <strong>
            {title}
            {lightning && <span className="selection-lightning"><i className="fa fa-bolt" /></span> }
            {lightning_wannabe && <span className="selection-lightning">"<i className="fa fa-bolt" />"</span> }
         </strong>
         <div>{description}</div>
         <small className="text-navy">{notes}</small>
         {rank !== undefined &&
         	<span className="rank">{rank}</span>
         }
      </div>
   </div>
);