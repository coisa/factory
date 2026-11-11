'use strict';

const picker = document.querySelector('#seam-choice');
const advice = document.querySelector('#seam-advice');
const strategies = {
  constructor: 'Start with ReflectionClassFactory or Factory::create(). Register an override at that explicit call boundary when needed.',
  static: 'Wrap the selected static construction method in CallableFactory and inject that factory. Direct static calls elsewhere keep their original behavior.',
  container: 'Pass your PSR-11 container to Factory. The container owns the value and its lifetime; use a callback when call-specific construction arguments are needed.',
  fake: 'Register a ValueFactory containing a fake that satisfies the consumer’s interface. Concrete final-type constraints still apply.',
};
if (picker && advice) {
  picker.addEventListener('change', () => {
    advice.textContent = strategies[picker.value];
  });
}
