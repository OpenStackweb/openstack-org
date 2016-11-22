import React from 'react';
import ReactDOM from 'react-dom';
import LinkButton from './LinkButton';

const AlphabetBar = ({
	className,
	label,
	selected,
	linkProvider,
	onLetterClicked
}) => {
	const alphabet = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];

	return (
		<ul className={className}>
			<li className="list-title">{label}:</li>			
			{alphabet.map(letter => (
				<li key={letter}>
					<LinkButton 
						link={linkProvider && linkProvider(letter)}
						active={letter === selected}
						eventKey={letter}
						onLinkClicked={onLetterClicked}
					>
						{letter}
					</LinkButton>
				</li>
			))}
		</ul>
	);
};

AlphabetBar.propTypes = {
	className: React.PropTypes.string,
	label: React.PropTypes.string,
	selected: React.PropTypes.string,
	linkCreator: React.PropTypes.func,
	onLetterClicked: React.PropTypes.func
};

export default AlphabetBar;