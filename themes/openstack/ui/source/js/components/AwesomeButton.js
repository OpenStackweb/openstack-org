import React from 'react';
import Button from '~core-components/button';

class AwesomeButton extends React.Component {

    clickButton(e) {
        alert('You clicked it');
    }

    render() {
        return (
            <div>
                <h2>Click this great button</h2>
                <Button onButtonClicked={this.clickButton}>Yeah</Button>
            </div>

        );
    }
}

export default AwesomeButton;
