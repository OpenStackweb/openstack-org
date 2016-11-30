import AnimateCSSAnimation from './AnimateCSSAnimation';
import './slide-out-right.less';

class SlideOutRight extends AnimateCSSAnimation {
	getName() {
		return 'slideOutRight';
	}
}

export default new SlideOutRight();