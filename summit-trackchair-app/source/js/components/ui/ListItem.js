import React from 'react';

export default ({
    title,
    description,
    notes,
    active
}) => (
    <li className={active ? 'active' : ''}>
        <h3>{title}</h3>
        <div>{description}</div>
        <div>{notes}</div>
    </li>
)