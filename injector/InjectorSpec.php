<?
namespace injector;

interface InjectorSpec{
	function provide();
	function isSingleton();
	function isFinal();
}

