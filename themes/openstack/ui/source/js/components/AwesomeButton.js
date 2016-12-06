import React from 'react';
import Button from '~core-components/button';
import buttonStyles from './awesome-button.module.scss';

class AwesomeButton extends React.Component {

    clickButton(e) {
        alert('You clicked it');
    }

    render() {
        return (
            <div className={buttonStyles.button}>
                <h2>Click this great button</h2>
                <Button onButtonClicked={this.clickButton}>Yeah</Button>
            </div>

        );
    }
}

export default AwesomeButton;
