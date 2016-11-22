import AnimateCSSAnimation from './AnimateCSSAnimation';
import './slide-in-left.less';

class SlideInLeft extends AnimateCSSAnimation {
	getName() {
		return 'slideInLeft';
	}
}

export default new SlideInLeft();